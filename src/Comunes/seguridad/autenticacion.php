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
        self::$logger->info("🔐 autenticacion::login() iniciado para: {$email}");

        $user = credencialesDB::credenciales($email, $password);
        if (!$user) {
            self::$logger->warning("❌ Usuario no encontrado: {$email}");
            return false;
        }
        self::$logger->debug("▶ Verificando password en PHP -> textoPlano='{$password}', hashEnBD='{$user->password_hash}'");
        if (!password_verify($password, $user->password_hash)) {
            $user = CredencialesDB::credenciales($email, $password);
            self::$logger->warning("❌ Password incorrecto para: {$email}");
            return false;
        }

        # Generar JWE = JWT firmado + cifrado AES-GCM (vida = SESSION_LIFETIME segs)
        $lifetime = env_int('SESSION_LIFETIME', 300);
        $jwe = encriptacion::generarJwe(['user_id' => $user->id, 'tipo_rol' => $user->tipo_rol], $lifetime);
        self::$logger->debug("🔑 JWE generado: {$jwe}");

        # Guardar en $_SESSION datos mínimos requeridos
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id']  = $user->id;
        $_SESSION['tipo_rol'] = $user->tipo_rol;
        self::$logger->info("🗄️ Datos de usuario en \$_SESSION para user_id={$user->id}");

        return true;
    }

    # Logout: limpiar $_SESSION y eliminar cookie auth_token.
    public static function logout(): void {
        self::initLogger();
        self::$logger->info("🚪 autenticacion::logout() iniciado");

        // Limpiar sesión PHP
        $_SESSION = [];
        self::$logger->debug("🧹 \$_SESSION limpiado");

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
        self::$logger->debug("🍪 Cookie 'auth_token' marcada para expiración");

        // Destruir sesión PHP
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            self::$logger->debug("🔒 session_destroy() ejecutado");
        }
    }

    # Verifica sesión y token JWE. Si invalida, redirige a /login.php. 
    # Retorna true si OK (sigue fluyendo la petición); si falla, redirige y hace exit.
    public static function revisarLogueoUsers(): bool {
        self::initLogger();

        # Comprueba flag en $_SESSION
        $loggedin = !empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
        if (!$loggedin) {
            self::$logger->warning("⚠️ revisarLogueoUsers(): no hay sesión iniciada en \$_SESSION");
            header('Location: /login.php');
            exit;
        }

        # Comprueba existencia de cookie “auth_token”
        $jwe = $_SESSION['auth_token'] ?? '';
        if (empty($jwe)) {
            self::$logger->warning("⚠️ revisarLogueoUsers(): no existe cookie auth_token");
            header('Location: /login.php');
            exit;
        }

        # Validar JWE (descifrar + verificar JWT)
        $claims = encriptacion::validarJwe($jwe);
        if ($claims === null) {
            self::$logger->warning("⚠️ revisarLogueoUsers(): JWE inválido o expirado");
            // Borrar cookie:
            setcookie('auth_token', '', ['expires'=>time()-3600,'path'=>'/','domain'=>$_ENV['APP_DOMAIN']??'','secure'=>env_bool('SESSION_COOKIE_SECURE'),'httponly'=>true,'samesite'=>'Lax']);
            // Limpiar sesión
            $_SESSION = [];
            header('Location: /login.php');
            exit;
        }

        # Comprobar coincidencia claims vs $_SESSION
        if ($claims['user_id'] != ($_SESSION['user_id'] ?? null)
            || $claims['tipo_rol'] != ($_SESSION['tipo_rol'] ?? null)
        ) {
            self::$logger->warning("⚠️ revisarLogueoUsers(): claims no coinciden con \$_SESSION");
            setcookie('auth_token', '', ['expires'=>time()-3600,'path'=>'/','domain'=>$_ENV['APP_DOMAIN']??'','secure'=>env_bool('SESSION_COOKIE_SECURE'),'httponly'=>true,'samesite'=>'Lax']);
            $_SESSION = [];
            header('Location: /login.php');
            exit;
        }

        # - Todo OK
        self::$logger->info("✅ revisarLogueoUsers(): token y sesión válidos para user_id={$claims['user_id']}");
        return true;
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