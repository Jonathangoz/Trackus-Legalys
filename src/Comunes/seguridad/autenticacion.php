<?php
// src/Comunes/seguridad/autenticacion.php (autentica: sesiones, tokens, parametros, usuarios, credenciales)
declare(strict_types=1);

namespace App\Comunes\seguridad;

use App\Comunes\middleware\credencialesDB;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class autenticacion {
    private static Logger $logger;

    # Inicializa el logger
    private static function initLogger(): void {
        if (!isset(self::$logger)) {
            self::$logger = loggers::createLogger();
        }
    }

    # Loguear al usuario, Retorna true si es OK, false si falla., genera JWE, envía cookie y guarda $_SESSION.
    public static function login(string $email, string $password): bool {
        self::initLogger();

        $user = credencialesDB::credenciales($email, $password);
        if (!$user) {
            self::$logger->warning("❌ Usuario no encontrado: {$email}");
            return false;
        }
        if (!password_verify($password, $user->password_hash)) {
            $user = CredencialesDB::credenciales($email, $password);
            self::$logger->warning("❌ Password incorrecto para: {$email}");
            return false;
        }

        # Generar JWE = JWT firmado + cifrado AES-GCM (vida = SESSION_LIFETIME segs)
        $lifetime = env_int('SESSION_LIFETIME', 300);
        $jwe = encriptacion::generarJwe(['user_id' => $user->id, 'tipo_rol' => $user->tipo_rol], $lifetime);

        # Guardar en $_SESSION datos mínimos requeridos
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id']  = $user->id;
        $_SESSION['tipo_rol'] = $user->tipo_rol;
        $_SESSION['auth_token'] = $jwe;

        # Enviar cookie al navegador
        setcookie(
            'auth_token',
            $jwe,
            [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'domain'   => $_ENV['APP_DOMAIN'] ?? '',
                'secure'   => env_bool('SESSION_COOKIE_SECURE', false),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );

        self::$logger->info("✅ Login exitoso para: {$email}, token creado");

        return true;
    }

    # Logout: limpiar $_SESSION y eliminar cookie auth_token.
    public static function logout(): void {
        self::initLogger();

        // Limpiar sesión PHP
        $_SESSION = [];

        // Expirar cookie “auth_token”
        setcookie(
            'auth_token',
            '',
            [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => $_ENV['APP_DOMAIN'] ?? '',
                'secure'   => env_bool('SESSION_COOKIE_SECURE'),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );

        // Destruir sesión PHP
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    # Verifica sesión y token JWE. Si invalida, redirige a /login.php. 
    # Retorna true si OK (sigue fluyendo la petición); si falla, redirige y hace exit.
    public static function revisarLogueoUsers(): bool {
        self::initLogger();
        
        // Debug
        self::$logger->info("🔍 Session ID actual: " . session_id());
        self::$logger->info("🔍 Session save path: " . session_save_path());
        self::$logger->info("🔍 Session data completa: " . json_encode($_SESSION));
        
        # ❌ NO uses empty() con arrays, usa isset()
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            self::$logger->warning("⚠️ No hay flag loggedin en sesión");
            self::redirectToLogin();
            return false;
        }
        
        # Si no hay token en sesión, buscar en cookie
        $jwe = $_SESSION['auth_token'] ?? $_COOKIE['auth_token'] ?? '';
        
        if (empty($jwe)) {
            self::$logger->warning("⚠️ No existe token");
            self::redirectToLogin();
            return false;
        }
        
        # Validar token
        $claims = encriptacion::validarJwe($jwe);
        if ($claims === null) {
            self::$logger->warning("⚠️ Token inválido");
            self::redirectToLogin();
            return false;
        }
        
        # Restaurar datos de sesión si se perdieron
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $claims['user_id'];
            $_SESSION['tipo_rol'] = $claims['tipo_rol'];
            $_SESSION['auth_token'] = $jwe;
            self::$logger->info("✅ Datos de sesión restaurados desde token");
        }
        
        return true;
    }

    private static function redirectToLogin(): void {
        self::logout();
        header('Location: /login.php');
        exit;
    }

    # verifica en la sesion el user_id
    public static function idUsuario(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    # verifica en la sesion el tipo_rol
    public static function rolUsuario(): ?string {
        return $_SESSION['tipo_rol'] ?? null;
    }
}