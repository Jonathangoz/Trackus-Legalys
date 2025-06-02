<?php
// src/controladores/control_logging.php
declare(strict_types=1);

namespace App\controladores;

use App\seguridad\autenticacion;
use App\seguridad\encriptacion;
use App\validaciones\validarlogin;
use App\DB\conexion;

class control_logging extends controlador_base
{
    /**
     * GET /login
     * Muestra el formulario de login.
     */
    public function showLoginForm(): void
    {
        if (autenticacion::checkUserIsLogged()) {
            $this->redirect('/dashboard');
        }

        include __DIR__ . '/../../public/logging.php';
    }

    /**
     * POST /login
     * Procesa el formulario de login.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        // 1) Validar CSRF
        $csrfForm = $_POST['csrf_token'] ?? '';
        $csrfSes  = $_SESSION['csrf_token'] ?? '';
        if (! hash_equals($csrfSes, $csrfForm)) {
            $_SESSION['login_errors'] = ['general' => 'Token CSRF inválido.'];
            $this->redirect('/login');
        }

        // 2) Recolectar datos
        $input = [
            'correo'      => trim($_POST['correo'] ?? ''),
            'contrasenia' => trim($_POST['contrasenia'] ?? ''),
        ];

        // 3) Validar con LoginValidator
        $errors = validarlogin::validate($input);
        if (! empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['old']          = ['correo' => $input['correo']];
            $this->redirect('/login');
        }

        // 4) Intentar login
        $ok = autenticacion::login($input['correo'], $input['contrasenia']);
        if (! $ok) {
            $_SESSION['login_errors'] = ['general' => 'Correo o contraseña inválidos.'];
            $_SESSION['old']          = ['correo' => $input['correo']];
            $this->redirect('/login');
        }

        // 5) Generar, firmar y cifrar token + guardar expiración en sesión
        $rawToken = encriptacion::generateRandomToken();
        $lifetime = intval($_ENV['SESSION_LIFETIME'] ?? 600); // por defecto 600s
        $secureToken = encriptacion::signEncryptAndStoreExpiry($rawToken, $lifetime);

        // 6) Guardar token en la sesión (opcional, redundancia)
        $_SESSION['auth_token'] = $secureToken;

        // 7) Insertar el token en PostgreSQL para poder revocar/consultar
        $userId = autenticacion::getUserId(); // asume que login() ya definió $_SESSION['user_id']
        $expiresAt = (new \DateTimeImmutable())
                        ->add(new \DateInterval("PT{$lifetime}S"))
                        ->format('Y-m-d H:i:sP');

        $db  = conexion::getInstance();
        $sql = "INSERT INTO user_tokens (user_id, token, expires_at)
                VALUES (:user_id, :token, :expires_at)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'user_id'    => $userId,
            'token'      => $secureToken,
            'expires_at' => $expiresAt
        ]);

        // 8) Enviar cookie segura al cliente
        setcookie(
            'auth_token',
            $secureToken,
            [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'domain'   => '',       // tu-dominio.com o vacío
                'secure'   => true,     // SOLO HTTPS
                'httponly' => true,     // JavaScript NO puede leerla
                'samesite' => 'Strict', // no se envía en peticiones cross-site
            ]
        );

        // 9) Redirigir según rol
        $rol = autenticacion::getUserRole();
        switch ($rol) {
            case 'ADMIN':
                header('Location: /dashboard');
                break;
            case 'ADMIN_TRAMITE':
                header('Location: /admin_tramite');
                break;
            case 'ABOGADO_1':
            case 'ABOGADO_2':
            case 'ABOGADO_3':
                header('Location: /abogado');
                break;
            case 'USUARIO':
                header('Location: /usuarios');
                break;
            default:
                autenticacion::logout();
                $_SESSION['login_errors'] = ['general' => 'Rol no reconocido.'];
                header('Location: /login');
                break;
        }
        exit;
    }

    /**
     * GET /logout
     * Cierra sesión y redirige a login.
     */
    public function logout(): void 
    {
        // 1) Regenerar ID de sesión
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        // 2) Borrar la cookie de sesión PHP
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // 3) Borrar cookie auth_token
        setcookie(
            'auth_token',
            '',
            [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );

        // 4) Destruir datos de sesión
        $_SESSION = [];
        session_destroy();

        // 5) Evitar caché
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        // 6) Redirigir a la página de presentación
        header("Location: /index.html");
        exit;
    }
}
