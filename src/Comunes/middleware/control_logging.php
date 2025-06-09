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
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Crear el logger en cada instancia para capturar todo dentro de esta clase
        $this->logger = loggers::createLogger();
        $this->logger->info("💼 control_logging::__construct() inicializado");
    }

    protected function redirect(string $url): void {
        $this->logger->debug("🔀 Redirigiendo a: {$url}");
        $this->redirect('/login');
        exit;
    }

    # GET /login - Muestra el formulario de login.
    public function vistaLogging(): void {
        $this->logger->info("🏷️  control_logging::vistaLogging() invocado");

        # Generar token CSRF (si no existe)
        $csrfToken = csrf::generarToken();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => isset($_SERVER['HTTP']),
                'cookie_samesite' => 'Lax'
            ]);
        }
        $this->logger->debug("🔐 Token CSRF generado: {$csrfToken}");

        # Incluir la vista de login
        $this->logger->info("📄 Incluyendo vista logging.php");
        include_once __DIR__ . '/../../../public/logging.php';
        $this->logger->info("✔️  Vista de login cargada");
    }

    # POST /login - Procesa el formulario de login.
    public function login(): void {
        $this->logger->info("🏷️  control_logging::login() invocado");

        # Asegurar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->logger->warning("⚠️  login() llamado con método {$_SERVER['REQUEST_METHOD']} (no POST), redirigiendo a /login");
            $this->redirect('/login');
        }

        # Recolectar datos de entrada
        $correoGenérico = trim($_POST['correo'] ?? '');
        $correoInstitu  = trim($_POST['correo_institucional'] ?? '');
        $password   = trim($_POST['password']);
        $this->logger->debug("▶ POST recibido: correo='{$correoGenérico}', correo_institucional='{$correoInstitu}', password_hash='{$password}'");

        # Decidir cuál correo usar
        if (!empty($correoGenérico) && filter_var($correoGenérico, FILTER_VALIDATE_EMAIL)) {
            $email = $correoGenérico;
            $this->logger->debug("➡️ Usando correo genérico para login: '{$email}'");
        }
        elseif (!empty($correoInstitu) && filter_var($correoInstitu, FILTER_VALIDATE_EMAIL)) {
            $email = $correoInstitu;
            $this->logger->debug("➡️ Usando correo institucional para login: '{$email}'");
        }
        else {
            $this->logger->warning("❌ Ningún correo válido enviado en POST");
            $_SESSION['login_errors'] = ['general' => 'Por favor ingresa un correo o correo institucional válido.'];
            $_SESSION['old']         = ['correo' => $correoGenérico, 'correo_institucional' => $correoInstitu];
            $this->redirect('/login');
        }

        # llama autenticación con el correo elegido
        $this->logger->info("🔐 Intentando autenticación para correo: {$email}");
        $ok = autenticacion::login($email, $password);
        if (!$ok) {
            $this->logger->warning("❌ Autenticación fallida para correo: {$email}");
            $_SESSION['login_errors'] = ['general' => 'Correo o contraseña inválidos.'];
            $_SESSION['old'] = ['correo' => $correoGenérico];
            $this->redirect('/login');
        }
        $this->logger->info("✅ Autenticación exitosa para correo: {$email}");

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
        $this->logger->debug("🔒 Token firmado y cifrado (con exp={$lifetime}s): {$secureToken}");

        $_SESSION['auth_token'] = $secureToken;
        session_regenerate_id(true);
        $this->logger->debug("🗄️  auth_token almacenado en \$_SESSION");



        # 6) Guardar token en base de datos para revocación posterior
    /*    $userId    = autenticacion2::idUsuario();
        $expiresAt = (new \DateTimeImmutable())
                        ->add(new \DateInterval("PT{$lifetime}S"))
                        ->format('Y-m-d H:i:sP');
        $this->logger->debug("⏳ Token expirará en: {$expiresAt}, user_id={$userId}");

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
    $this->logger->debug("👤 Rol del usuario autenticado: {$rol}");

    switch ($rol) {
        case 'ADMIN':
            $this->logger->info("↪️  Redirigiendo a controlador ADMIN");
            $administrador = new \App\Modulos\Controladores\control_admin(); # Ruta donde dirige el controlador y a asu Modulo
            $administrador->handle('/dashboard', 'POST');
            $this->logger->info("✔️  control_admin->handle() completado");
            break;

        case 'ADMIN_TRAMITE':
            $this->logger->info("↪️  Redirigiendo a controlador ADMIN_TRAMITE");
            $controller = new \App\Modulos\Controladores\control_adminTramites(); # Ruta donde dirige el controlador y a asu Modulo
            $controller->handle('/cobrocoactivo', 'POST');
            $this->logger->info("✔️  control_adminTramites->handle() completado");
            break;

        case 'ABOGADO':
            $this->logger->info("↪️  Redirigiendo a controlador ABOGADOS");
            $controller = new \App\Modulos\Controladores\control_abogados(); # Ruta donde dirige el controlador y a asu Modulo
            $controller->handle('/deudores', 'POST');
            $this->logger->info("✔️  control_abogados->handle() completado");
            break;

        case 'DEUDOR':
            $this->logger->info("↪️  Redirigiendo a controlador USUARIO");
            $controller = new \App\Modulos\Controladores\control_deudores(); # Ruta donde dirige el controlador y a asu Modulo
            $controller->handle('/consultas', 'POST');
            $this->logger->info("✔️  control_usuarios->handle() completado");
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
        $this->logger->info("🚪 control_logging::logout() invocado");

        # Destruir datos de sesión
        $_SESSION = [];
        $this->logger->debug("🧹 \$_SESSION limpiado");

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
            $this->logger->debug("🔒 session_destroy() llamado");
        }

        $this->logger->info("✔️  Sesión destruida, finalizando petición logout");
       # $this->redirect('/login');
        header('Location: /login');
        exit;
    }
}