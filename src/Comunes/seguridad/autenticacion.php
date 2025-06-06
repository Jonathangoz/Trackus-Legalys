<?php
// src/Comunes/seguridad/autenticacion.php
#declare(strict_types=1);

namespace App\Comunes\seguridad;

use App\Comunes\middleware\credencialesDB;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class autenticacion {
    /** @var Logger */
    private static Logger $logger;

    /**
     * Inicializa el logger si aún no existe
     */
    private static function initLogger(): void {
        if (!isset(self::$logger)) {
            self::$logger = loggers::createLogger();
        }
    }

    /**
     * Intenta loguear al usuario. Retorna true si OK, false si falla.
     */
    public static function login(string $email, string $password): bool {
        self::initLogger();
        self::$logger->info("🔐 autenticacion::login() iniciado para: {$email}");

        $user = credencialesDB::credenciales($email, $password);
        if (!$user) {
            self::$logger->warning("❌ Usuario no encontrado: {$email}");
            return false;
        }
        if (!password_verify($password, $user->password_hash)) {
            self::$logger->warning("❌ Password incorrecto para: {$email}");
            return false;
        }

        // 1) Regenerar ID de sesión
        session_regenerate_id(true);
        self::$logger->debug("🔄 session_regenerate_id(true) ejecutado, nuevo session_id: " . session_id());

        // 2) Generar y firmar token
        $tokens = encriptacion::tokenRandom();
        $firmaToken = encriptacion::lenToken($tokens);
        self::$logger->debug("🔑 Token generado y firmado: {\$firmaToken}");

        // 3) Guardar en sesión los datos mínimos
        $_SESSION['loggedin']   = true;
        $_SESSION['user_id']    = $user->id;
        $_SESSION['user_type']  = $user->tipo_usuario;
        $_SESSION['correo']     = $user->correo;
        $_SESSION['nombres']    = $user->nombres;
        $_SESSION['apellidos']  = $user->apellidos;
        $_SESSION['tipo_rol']   = $user->tipo_rol;
        $_SESSION['auth_token'] = $firmaToken;
        self::$logger->info("🗄️ Datos de usuario almacenados en \$_SESSION para user_id={\$user->id}");

        return true;
    }

    /**
     * Cierra sesión de forma segura.
     */
    public static function logout(): void {
        self::initLogger();
        self::$logger->info("🚪 autenticacion::logout() iniciado");

        // Limpiar sesión
        $_SESSION = [];
        self::$logger->debug("🧹 \$_SESSION limpiado");

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            self::$logger->debug("🍪 Cookies de sesión marcadas para expiración");
        }

        session_destroy();
        self::$logger->debug("🔒 session_destroy() ejecutado");
    }

    /**
     * Devuelve true si hay un usuario logueado y el token firmado es válido.
     */
    public static function revisarLogueoUsers(): bool {
        self::initLogger();
        $loggedin = !empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
        $tokenSet = !empty($_SESSION['auth_token']);
        self::$logger->debug("👀 revisarLogueoUsers(): loggedin={" . ($loggedin ? 'true' : 'false') . "}, tokenSet={" . ($tokenSet ? 'true' : 'false') . "}");
        if (! $loggedin || ! $tokenSet) {
            return false;
        }
        $valid = encriptacion::descencrriptarVerificar($_SESSION['auth_token']) !== null;
        self::$logger->info("✅ revisarLogueoUsers(): token válido={" . ($valid ? 'true' : 'false') . "}");
        return $valid;
    }

    /**
     * Retorna el ID del usuario actual o null.
     */
    public static function idUsuario(): ?int {
        self::initLogger();
        $id = $_SESSION['user_id'] ?? null;
        self::$logger->debug("👤 idUsuario(): {\$id}");
        return $id;
    }

    /**
     * Retorna el rol del usuario actual o null.
     */
    public static function rolUsuario(): ?string {
        self::initLogger();
        $rol = $_SESSION['tipo_rol'] ?? null;
        self::$logger->debug("🛡 rolUsuario(): {\$rol}");
        return $rol;
    }

    /**
     * Retorna el nombre completo del usuario.
     */
    public static function nombresUsuario(): ?string {
        self::initLogger();
        if (empty($_SESSION['nombres']) || empty($_SESSION['apellidos'])) {
            self::$logger->warning("⚠️ nombresUsuario(): datos parciales en sesión");
            return null;
        }
        $full = trim($_SESSION['nombres'] . ' ' . $_SESSION['apellidos']);
        self::$logger->debug("📛 nombresUsuario(): {\$full}");
        return $full;
    }
}
