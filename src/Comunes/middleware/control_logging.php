<?php
# src/Comunes/middleware/control_logging.php (Despues de index.php llega ha este Subcontroller que asigna ruta de roles y envia a los controladores segun rol)
declare(strict_types=1);

namespace App\Comunes\middleware;

use App\Comunes\seguridad\autenticacion;
use App\Comunes\seguridad\encriptacion;
use App\Comunes\utilidades\loggers;
#use App\Comunes\DB\conexion;
use Monolog\Logger;

class control_logging { 
    /** @var Logger
     * @param string $uri La URI solicitada
     * @param string $method El método HTTP
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

        # Incluir la vista de login
        include_once __DIR__ . '/../../../public/logging.php';
    }

    # POST /login - Procesa el formulario de login.
    public function login(): void {

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

        # Verificar que el login fue exitoso y tenemos los datos necesarios
        $userId = autenticacion::idUsuario();
        $userRole = autenticacion::rolUsuario();
        
        if (empty($userId) || empty($userRole)) {
            $this->logger->error("🚨 Login aparentemente exitoso pero faltan datos: userId={$userId}, role={$userRole}");
            $_SESSION['login_errors'] = ['general' => 'Error en la autenticación.'];
            $this->redirect('/login');
        }

        # Generar un nuevo JWE basado en idUsuario y tipo_rol
        $lifetime = intval($_ENV['SESSION_LIFETIME'] ?? 600);
        $customClaims = [
            'user_id'  => $userId,
            'tipo_rol' => $userRole,
        ];
        $secureToken = encriptacion::generarJwe(
            $customClaims,
            $lifetime,
        );

        $_SESSION['auth_token'] = $secureToken;
        $_SESSION['user_id'] = $userId;  // Agregar explícitamente
        $_SESSION['user_role'] = $userRole;  // Agregar explícitamente

        $this->logger->info("✅ Datos de sesión guardados: user_id={$userId}, role={$userRole}");
        $this->logger->info("✅ Token generado: " . substr($secureToken, 0, 20) . "...");
        
        # Redirigir según el rol del usuario
        $redirectUrl = $this->getRedirectUrlByRole($userRole);
        $this->logger->info("🎯 Redirigiendo usuario con rol {$userRole} a: {$redirectUrl}");
        $this->redirect($redirectUrl);
    }

    /**
     * Determina la URL de redirección según el rol del usuario
     */
    private function getRedirectUrlByRole(string $role): string {
        switch ($role) {
            case 'ADMIN':
                return '/dashboard';
            case 'ADMIN_TRAMITE':
                return '/asignacion';
            case 'ABOGADO':
                return '/cobrocoactivo';
            case 'DEUDOR':
                return '/consultas';
            default:
                $this->logger->warning("🚫 Rol no reconocido para redirect: {$role}");
                return '/login';
        }
    }

    public function handleAuthenticatedRequest(string $uri, string $method): void {
        # Debug de sesión
        $this->logger->info("🔍 Verificando autenticación para {$uri}");
        $this->logger->info("🔍 Session status: " . session_status());
        $this->logger->info("🔍 Auth token exists: " . (isset($_SESSION['auth_token']) ? 'SI' : 'NO'));
        $this->logger->info("🔍 User ID exists: " . (isset($_SESSION['user_id']) ? 'SI' : 'NO'));
        
        # Verificar si el usuario está autenticado
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            $this->logger->warning("🚫 Datos de sesión: " . json_encode($_SESSION));
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        $this->logger->info("🔐 Usuario autenticado con rol: {$rol} accediendo a: {$uri}");

        # Redirigir según rol y URI
        switch ($rol) {
            case 'ADMIN':
                $this->logger->info("↪️  Redirigiendo a controlador ADMIN");
                $administrador = new \App\Modulos\Controladores\control_admin();
                $administrador->handle($uri, $method);
                break;

            case 'ADMIN_TRAMITE':
                $this->logger->info("↪️  Redirigiendo a controlador ADMIN_TRAMITE");
                $controller = new \App\Modulos\Controladores\control_adminTramites();
                $controller->handle($uri, $method);
                break;

            case 'ABOGADO':
                $this->logger->info("↪️  Redirigiendo a controlador ABOGADOS");
                $controller = new \App\Modulos\Controladores\control_abogados();
                $controller->handle($uri, $method);
                break;

            case 'DEUDOR':
                $this->logger->info("↪️  Redirigiendo a controlador USUARIO");
                $controller = new \App\Modulos\Controladores\control_deudores();
                $controller->handle($uri, $method);
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

     $this->logger->info("✔️  Sesión destruida, finalizando petición logout");
    # $this->redirect('/login');
        header('Location: /login');
        exit;
    }
}