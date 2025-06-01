<?php
// src/Services/AuthService.php
declare(strict_types=1);

namespace App\servicios;

use App\modelos\modeloBase;

class autenticacion
{
    /**
     * Intenta loguear al usuario. Retorna true si OK, false si falla.
     * - Busca el User por correo.
     * - Verifica password.
     * - Regenera sesión, genera token firmado y almacena datos en $_SESSION.
     */
    public static function login(string $correo, string $contrasenia): bool
    {
        $user = $contrasenia::findByEmail($correo);
        if (! $user) {
            return false;
        }
        if (! password_verify($contrasenia, $user->password_hash)) {
            return false;
        }

        // 1) Regenerar ID de sesión para evitar fixation
        session_regenerate_id(true);

        // 2) Generar un token aleatorio y firmarlo
        $rawToken = encriptacion::generateRandomToken();
        $signedToken = encriptacion::signToken($rawToken);

        // 3) Guardar en sesión los datos mínimos
        $_SESSION['loggedin']      = true;
        $_SESSION['user_id']       = $user->id;
        $_SESSION['user_type']     = $user->tipo_usuario; // 'funcionario' o 'usuario'
        $_SESSION['correo']        = $user->correo;
        $_SESSION['nombres']       = $user->nombres;
        $_SESSION['apellidos']     = $user->apellidos;
        $_SESSION['tipo_rol']      = $user->tipo_rol;
        $_SESSION['auth_token']    = $signedToken;

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
    public static function checkUserIsLogged(): bool
    {
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            return false;
        }
        if (empty($_SESSION['auth_token'])) {
            return false;
        }
        return encriptacion::verifySignedToken($_SESSION['auth_token']) !== null;
    }

    /**
     * Retorna el rol del usuario actual (o null).
     */
    public static function getUserRole(): ?string
    {
        return $_SESSION['tipo_rol'] ?? null;
    }

    /**
     * Retorna el nombre completo del usuario.
     */
    public static function getUserFullName(): ?string
    {
        if (empty($_SESSION['nombres']) || empty($_SESSION['apellidos'])) {
            return null;
        }
        return trim($_SESSION['nombres'] . ' ' . $_SESSION['apellidos']);
    }
}
