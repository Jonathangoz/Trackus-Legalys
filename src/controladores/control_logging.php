<?php
// src/Controllers/AuthController.php
declare(strict_types=1);

namespace App\controladores;

use App\servicios\autenticacion;
use App\validaciones\validarlogin;

class control_logging extends controlador_base
{
    /**
     * GET /login
     * Muestra el formulario de login.
     */
    public function showLoginForm(): void
    {
        // Si ya está logueado, enviar al dashboard
        if (autenticacion::checkUserIsLogged()) {
            $this->redirect('/dashboard');
        }

        // Incluir la vista en public/login.php
        include __DIR__ . '/../../public/login.php';
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

        // 1) Validar CSRF antes de cualquier otra cosa
        $csrfForm = $_POST['csrf_token'] ?? '';
        $csrfSes  = $_SESSION['csrf_token'] ?? '';
        if (! hash_equals($csrfSes, $csrfForm)) {
            $_SESSION['login_errors'] = ['general' => 'Token CSRF inválido.'];
            $this->redirect('/login');
        }

        // 2) Recolectar datos brutos del formulario
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
            // Si falla: credenciales incorrectas o usuario no existe
            $_SESSION['login_errors'] = ['general' => 'Correo o contraseña inválidos.'];
            $_SESSION['old']          = ['correo' => $input['correo']];
            $this->redirect('/login');
        }

        // 5) Login exitoso: redirigir según tipo de rol
        $rol = autenticacion::getUserRole();
        switch ($rol) {
            case 'ADMIN':
                $this->redirect('/dashboard');
                break;
            case 'ADMIN_TRAMITE':
                $this->redirect('/admin_tramite'); // ajusta ruta a tu vista
                break;
            case 'ABOGADO_1':
            case 'ABOGADO_2':
            case 'ABOGADO_3':
                $this->redirect('/abogado');       // ajusta ruta a tu vista
                break;
            case 'USUARIO':
                $this->redirect('/usuarios');      // ajusta ruta a tu vista
                break;
            default:
                // Si el rol no está reconocido, cerrar sesión y mostrar error
                autenticacion::logout();
                $_SESSION['login_errors'] = ['general' => 'Rol no reconocido.'];
                $this->redirect('/login');
        }
    }

    /**
     * GET /logout
     * Cierra sesión y redirige a login.
     */
    public function logout(): void {
        // 1) Regenerar ID de sesión para prevenir session fixation
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        // 2) Borrar la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),    // nombre de la cookie de sesión
                '',                // valor vacío
                time() - 42000,    // expirada en el pasado
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // 3) Destruir datos de la sesión en el servidor
        $_SESSION = [];
        session_destroy();

        // 4) Evitar que el navegador almacene en caché las páginas protegidas
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0
        header("Expires: 0"); // Proxies

        // 5) Redirigir a la página estática de presentación
        header("Location: /index.html");
        exit;
    }
}
