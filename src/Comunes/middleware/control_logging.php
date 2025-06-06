<?php
// src/Comunes/middleware/control_logging.php

#declare(strict_types=1);

namespace App\Comunes\middleware;

use App\Comunes\seguridad\autenticacion;
use App\Comunes\seguridad\encriptacion;
use App\Comunes\validaciones\validarlogin;
use App\Comunes\seguridad\csrf;
use App\Comunes\DB\conexion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_logging
{
    /** @var Logger */
    private Logger $logger;

    public function __construct()
    {
        // Crear el logger en cada instancia para capturar todo dentro de esta clase
        $this->logger = loggers::createLogger();
        $this->logger->info("💼 control_logging::__construct() inicializado");
    }

    protected function redirect(string $url): void
    {
        $this->logger->debug("🔀 Redirigiendo a: {$url}");
        header("Location: {$url}");
        exit;
    }

    /**
     * GET /login
     * Muestra el formulario de login.
     */
    public function vistaLogging(): void
    {
        $this->logger->info("🏷️  control_logging::vistaLogging() invocado");

        // Si ya está logueado, no mostrar login
        if (autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("⚠️  Usuario ya logueado, redirigiendo a /dashboard");
            $this->redirect('/dashboard');
        }

        // Generar token CSRF (si no existe)
        $csrfToken = csrf::generarToken();
        $this->logger->debug("🔐 Token CSRF generado: {$csrfToken}");

        // Incluir la vista de login
        $this->logger->info("📄 Incluyendo vista logging.php");
        include __DIR__ . '/../../../public/logging.php';
        $this->logger->info("✔️  Vista de login cargada");
    }

    /**
     * POST /login
     * Procesa el formulario de login.
     */
    public function login(): void
    {
        $this->logger->info("🏷️  control_logging::login() invocado");

        // Asegurar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->logger->warning("⚠️  login() llamado con método {$_SERVER['REQUEST_METHOD']} (no POST), redirigiendo a /login");
            $this->redirect('/login');
        }

        // 1) Recolectar datos de entrada
        $input = [
            'correo'      => trim($_POST['correo'] ?? ''),
            'contrasenia' => trim($_POST['contrasenia'] ?? ''),
        ];
        $this->logger->debug("📥 Datos recibidos en login():", $input);

        // 2) Validar campos no vacíos
      /*  $errors = validarlogin::validarCampos($input);
        if (!empty($errors)) {
            $this->logger->warning("❌ Errores de validación en login():", $errors);
            $_SESSION['login_errors'] = $errors;
            $_SESSION['old']          = ['correo' => $input['correo']];
            $this->redirect('/login');
        }
        $this->logger->debug("✔️  Validación de campos OK"); */

        // 3) Intentar login con autenticacion::login()
        $this->logger->info("🔐 Intentando autenticación para correo: {$input['correo']}");
        $ok = autenticacion::login($input['correo'], $input['contrasenia']);
        if (!$ok) {
            $this->logger->warning("❌ Autenticación fallida para correo: {$input['correo']}");
            $_SESSION['login_errors'] = ['general' => 'Correo o contraseña inválidos.'];
            $_SESSION['old']          = ['correo' => $input['correo']];
            $this->redirect('/login');
        }
        $this->logger->info("✅ Autenticación exitosa para correo: {$input['correo']}");

        // 4) Generar token aleatorio y firmar/cifrar con expiración
        $rawToken = encriptacion::tokenRandom();
        $lifetime = intval($_ENV['SESSION_LIFETIME'] ?? 600);
        $this->logger->debug("🔑 Generando token aleatorio para sesión, lifetime={$lifetime}s: {$rawToken}");
        $secureToken = encriptacion::firmaEncriptadaExpiracion($rawToken, $lifetime);
        $this->logger->debug("🔒 Token firmado y cifrado: {$secureToken}");

        // 5) Guardar token en sesión
        $_SESSION['auth_token'] = $secureToken;
        $this->logger->debug("🗄️  auth_token almacenado en \$_SESSION");

        // 6) Guardar token en base de datos para revocación posterior
    /*    $userId    = autenticacion::idUsuario();
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

        // 7) Enviar cookie secure auth_token
        setcookie(
            'auth_token',
            $secureToken,
            [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,  // ajustar a true en producción HTTPS
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
        $this->logger->info("🍪 Cookie 'auth_token' enviada al cliente");

        // 8) Redirigir según rol del usuario
        $rol = autenticacion::rolUsuario();
        $this->logger->debug("👤 Rol del usuario autenticado: {$rol}");

        switch ($rol) {
            case 'ADMIN':
                $this->logger->info("↪️  Redirigiendo a controlador ADMIN");
                $administrador = new \App\Modulos\Controladores\control_admin();
                $administrador->handle('/dashboard', 'POST');
                $this->logger->info("✔️  control_admin->handle() completado");
                break;

            case 'ADMIN_TRAMITE':
                $this->logger->info("↪️  Redirigiendo a controlador ADMIN_TRAMITE");
                $controller = new \App\Modulos\Controladores\control_adminTramites();
                $controller->handle('/login', 'POST');
                $this->logger->info("✔️  control_adminTramites->handle() completado");
                break;

            case 'ABOGADO_1':
            case 'ABOGADO_2':
            case 'ABOGADO_3':
                $this->logger->info("↪️  Redirigiendo a controlador ABOGADOS");
                $controller = new \App\Modulos\Controladores\control_abogados();
                $controller->handle('/login', 'POST');
                $this->logger->info("✔️  control_abogados->handle() completado");
                break;

            case 'USUARIO':
                $this->logger->info("↪️  Redirigiendo a controlador USUARIO");
                $controller = new \App\Modulos\Controladores\control_usuarios();
                $controller->handle('/login', 'POST');
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

    /**
     * GET /logout
     * Cierra sesión y sale.
     */
    public function logout(): void {
        $this->logger->info("🚪 control_logging::logout() invocado");

        // 1) Destruir datos de sesión
        $_SESSION = [];
        $this->logger->debug("🧹 \$_SESSION limpiado");

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $this->logger->debug("🔒 session_destroy() llamado");
        }

        // 2) Ejecutar headers de no-cache y redirección (si se desea)
        // (Están comentados en tu código original, así que solo salimos)
        $this->logger->info("✔️  Sesión destruida, finalizando petición logout");
        exit;
    }
}
