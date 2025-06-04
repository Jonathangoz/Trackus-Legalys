<?php
// src/Comunes/seguridad/autenticacion.php
#declare(strict_types=1);

namespace App\Comunes\seguridad;

use App\Comunes\middleware\credencialesDB;

class autenticacion
{
    /**
     * Intenta loguear al usuario. Retorna true si OK, false si falla.
     * - Busca el User por correo.
     * - Verifica password.
     * - Regenera sesión, genera token firmado y almacena datos en $_SESSION.
     */
    public static function login(string $email, string $password): bool
    {
        $user = credencialesDB::credenciales($email, $password);
        if (! $user) {
            return false;
        }
        if (! password_verify($password, $user->password_hash)) {
            return false;
        }

        // 1) Regenerar ID de sesión para evitar fixation
        session_regenerate_id(true);

        // 2) Generar un token aleatorio y firmarlo
        $tokens = encriptacion::tokenRandom();
        $firmaToken = encriptacion::lenToken($tokens);

        // 3) Guardar en sesión los datos mínimos
        $_SESSION['loggedin']      = true;
        $_SESSION['user_id']       = $user->id;
        $_SESSION['user_type']     = $user->tipo_usuario; // 'funcionario' o 'usuario'
        $_SESSION['correo']        = $user->correo;
        $_SESSION['nombres']       = $user->nombres;
        $_SESSION['apellidos']     = $user->apellidos;
        $_SESSION['tipo_rol']      = $user->tipo_rol;
        $_SESSION['auth_token']    = $firmaToken;

        return true;
    }

    /**
     * Cierra sesión de forma segura.
     */
    public static function logout(): void
    {
        $_SESSION = [];
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
        session_destroy();
    }

    /**
     * Devuelve true si hay un usuario logueado y el token firmado es válido.
     */
    public static function revisarLogueoUsers(): bool
    {
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            return false;
        }
        if (empty($_SESSION['auth_token'])) {
            return false;
        }
        return encriptacion::verificarToken($_SESSION['auth_token']) !== null;
    }

    /**
     * Retorna el rol del usuario actual (o null).
     */
    public static function idUsuario(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }    

    /**
     * Retorna el rol del usuario actual (o null).
     */
    public static function rolUsuario(): ?string
    {
        return $_SESSION['tipo_rol'] ?? null;
    }

    /**
     * Retorna el nombre completo del usuario.
     */
    public static function nombresUsuario(): ?string
    {
        if (empty($_SESSION['nombres']) || empty($_SESSION['apellidos'])) {
            return null;
        }
        return trim($_SESSION['nombres'] . ' ' . $_SESSION['apellidos']);
    }
}
