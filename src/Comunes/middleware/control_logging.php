<?php
# src/Comunes/middleware/control_logging.php (Despues de index.php llega ha este Subcontroller que asigna ruta de roles y envia a los controladores segun rol)
declare(strict_types=1);

namespace App\Comunes\middleware;

use App\Comunes\seguridad\autenticacion;
use App\Comunes\seguridad\encriptacion;
use App\Comunes\seguridad\csrf;
#use App\Comunes\DB\conexion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_logging { 
    /** @var Logger
     *  @param string $uri    Ruta completa recibida /asigancion
     *  @param string $method "GET" o "POST"
     */
    private Logger $logger;

public function __construct() {
    # Crear el logger en cada instancia para capturar todo dentro de esta clase
    $this->logger = loggers::createLogger();  
    } 

    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }


    # GET /login - Muestra el formulario de login.
    public function vistaLogging(): void {

        # Generar token CSRF (si no existe)
        $csrfToken = csrf::generarToken();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => isset($_SERVER['HTTP']),
                'cookie_samesite' => 'Lax'
            ]);
        }

        # Incluir la vista de login
        include_once __DIR__ . '/../../../public/logging.php';
    }

    # POST /login - Procesa el formulario de login.
    public function login(string $uri = ''): void {

        # Recolectar datos de entrada
        $correoGenérico = trim($_POST['correo'] ?? '');
        $correoInstitu  = trim($_POST['correo_institucional'] ?? '');
        $password   = trim($_POST['password']);

        # Decidir cuál correo usar
        if (!empty($correoGenérico) && filter_var($correoGenérico, FILTER_VALIDATE_EMAIL)) {
            $email = $correoGenérico;
        }
        elseif (!empty($correoInstitu) && filter_var($correoInstitu, FILTER_VALIDATE_EMAIL)) {
            $email = $correoInstitu;
        }
        else {
            $_SESSION['login_errors'] = ['general' => 'Por favor ingresa un correo o correo institucional válido.'];
            $_SESSION['old']         = ['correo' => $correoGenérico, 'correo_institucional' => $correoInstitu];
            $this->redirect('/login');
        }

        # llama autenticación con el correo elegido
        $ok = autenticacion::login($email, $password);
        if (!$ok) {
            $_SESSION['login_errors'] = ['general' => 'Correo o contraseña inválidos.'];
            $_SESSION['old'] = ['correo' => $correoGenérico];
            $this->redirect('/login');
        }

        # Generar un nuevo JWE basado en idUsuario y tipo_rol
        $lifetime = intval($_ENV['SESSION_LIFETIME'] ?? 600);
        $customClaims = [
            'user_id'  => autenticacion::idUsuario(),
            'tipo_rol' => autenticacion::rolUsuario(),
        ];
        $secureToken = encriptacion::generarJwe(
            $customClaims,
            $lifetime,
        );

        $_SESSION['auth_token'] = $secureToken;
        session_regenerate_id(true);



        # 6) Guardar token en base de datos para revocación posterior
    /*   $userId    = autenticacion2::idUsuario();
        $expiresAt = (new \DateTimeImmutable())
                        ->add(new \DateInterval("PT{$lifetime}S"))
                        ->format('Y-m-d H:i:sP'); 

        try {
            $db  = conexion::instanciaDB();
            $sql = "INSERT INTO user_tokens (user_id, token, expires_at)
                    VALUES (:user_id, :token, :expires_at)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'user_id'    => $userId,
                'token'      => $secureToken,
                'expires_at' => $expiresAt
            ]);
            $this->logger->info("💾 Token insertado en user_tokens para user_id={$userId}");
        } catch (\Throwable $e) {
            $this->logger->error("🚨 Error al insertar token en DB: " . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            // Aunque falle la inserción, seguimos para no impedir el login
        } */

    # Redirigir según rol del usuario al controldor corespondiente y sus Modulos
    $rol = autenticacion::rolUsuario();
    $metodo = $_SERVER['REQUEST_METHOD'];    // "GET", "POST", etc.

    switch ($rol) {
        case 'ADMIN':
            $this->logger->info("↪️  Redirigiendo a controlador ADMIN");
            $administrador = new \App\Modulos\Controladores\control_admin(); # Ruta donde dirige el controlador y a asu Modulo
            $administrador->handle('/dashboard', $metodo);
            break;

        case 'ADMIN_TRAMITE':
            $this->logger->info("↪️  Redirigiendo a controlador ADMIN_TRAMITE");
            $controller = new \App\Modulos\Controladores\control_adminTramites();

            # Obtengo la URI completa y separo sólo la parte tras /ADMIN_TRAMITE
            $fullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $prefix   = '/ADMIN_TRAMITE';

            # Extraigo el sub‐path que viene después de “/ADMIN_TRAMITE”
            $subPath = strtolower(rtrim(substr($fullPath, strlen($prefix)), '/'));


            $this->logger->info("🔍 Ruta recibida: {$fullPath}");
            // debug de Monolog justo antes del switch
$this->logger->info("🛠️  DEBUG Rutas:");
$this->logger->info("   raw REQUEST_URI: "  . $_SERVER['REQUEST_URI']);
$this->logger->info("   extraído fullPath: " . $fullPath);
$this->logger->info("   después subPath: "   . $subPath);
$this->logger->info("   método HTTP: "      . $_SERVER['REQUEST_METHOD']);
            $this->logger->info("🔍 Rol del usuario: {$rol}");
            switch ($subPath) {
                case '':
                case '/asignacion':
                    $controller->handle('/asignacion', $_SERVER['REQUEST_METHOD']);
                    $this->logger->info("➡️  Redirigiendo a /asignacion");
                    break;

                case '/crearcasos':
                    $controller->handle('/crearcasos', $_SERVER['REQUEST_METHOD']);
                    
                    break;
                case '/registros':
                    $controller->handle('/registros', $_SERVER['REQUEST_METHOD']);
                    break;

                // Cualquier otra ruta => error y redirect
                default:
                    $this->logger->info("🔍 Ruta no reconocida: {$subPath}");
                    $this->logger->error("🚫 Ruta no reconocida para ADMIN_TRAMITE:");
                    $this->redirect('/login');
                    break;
            }

            return;

        case 'ABOGADO':
            $this->logger->info("↪️  Redirigiendo a controlador ABOGADOS");
            $controller = new \App\Modulos\Controladores\control_abogados(); # Ruta donde dirige el controlador y a asu Modulo
            $controller->handle('/deudores', 'POST');
            break;

        case 'DEUDOR':
            $this->logger->info("↪️  Redirigiendo a controlador USUARIO");
            $controller = new \App\Modulos\Controladores\control_deudores(); # Ruta donde dirige el controlador y a asu Modulo
            $controller->handle('/consultas', 'POST');
            break;

        default:
            $this->logger->warning("🚫 Rol no reconocido: '{$rol}'. Cerrando sesión.");
            autenticacion::logout();
            $_SESSION['login_errors'] = ['general' => 'Rol no reconocido.'];
            $this->redirect('/login');
            break;
    }
}

    # GET /logout Cierra sesión y sale al logging.php (vista - formulario login)
    public function logout(): void {

        # Destruir datos de sesión
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {

        # Para uso en HTTPs , para mas seguridad (destruir todas las cookies y el auth_token)
            if (ini_get("session.use_cookies")) {
                setcookie(
            'auth_token',
            '',
            [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,  // ajustar a true en producción HTTPS
                'httponly' => true,
                'samesite' => 'Lax',
                ] 
                ); 
            }
            # Destruyr token csrf
            unset($_SESSION['csrf_token']);
            # Destruye toda la Sesion
            session_destroy();
        }

    # $this->logger->info("✔️  Sesión destruida, finalizando petición logout");
    # $this->redirect('/login');
        header('Location: /login');
        exit;
    }
}