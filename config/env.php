<?php
#config/env.php (verivifa parametros de .env, no esten vacios o null)
#declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv; //(libreria para dar lectura a .env)
use Dotenv\Exception\ValidationException;

try {
    // DETECCIÓN DE ENTORNO (siempre “development” por defecto)
    $environment = getenv('APP_ENV') ?: 'development';

    //En desarrollo local, cargar .env si existe
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }
        //VALIDACIONES BÁSICAS OBLIGATORIAS (solo desarrollo)
        $dotenv->required([
            'APP_NAME',
            'APP_ENV',
            'APP_DEBUG',
            'APP_URL',
            'APP_KEY',
            'SECRET_KEY',
        ])->notEmpty();

        $dotenv->required([
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
        ])->notEmpty();

        $dotenv->required('DB_PORT')->isInteger();

    //VALIDACIONES ADICIONALES DE DESARROLLO
    if (isset($dotenv)) {
        // Booleanos
        $dotenv->required([
            'APP_DEBUG',
            'MAINTENANCE_MODE',
        ])->isBoolean();

        // Enteros
        $dotenv->required([
            'DB_PORT',
            'CACHE_DEFAULT_TTL',
            'SESSION_LIFETIME',
        ])->isInteger();

        // URLs válidas
        $urlvalido = ['APP_URL'];
        foreach ($urlvalido as $valido) {
            if (!empty($_ENV[$valido]) && !filter_var($_ENV[$valido], FILTER_VALIDATE_URL)) {
                throw new ValidationException("$valido debe ser una URL válida");
            }
        }

        // Emails válidos
        $emailvalido = ['MAIL_FROM_ADDRESS', 'MAIL_USERNAME'];
        foreach ($emailvalido as $valido) {
            if (!empty($_ENV[$valido]) && !filter_var($_ENV[$valido], FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException("$valido debe ser un email válido");
            }
        }

        // Validar longitud, Entropia real Base64 mínima de claves secretas
        $secretKeys = [
            'APP_KEY'         => 32,
            'ENCRYPTION_KEY'  => 32,
            'SECRET_KEY'      => 32,
            'SECRET_KEY2'     => 32,
        ];
        $raw = $_ENV['SECRET_KEY'] ?? '' && $_ENV['SECRET_KEY2'] ?? '' && $_ENV['APP_KEY'] && $_ENV['ENCRYPTION_KEY'];
            if (strpos($raw, 'base64:') === 0) {
                $secretKey = base64_decode(substr($raw, 7), true);
                if ($secretKey === false || strlen($secretKey) !== 32) {
                    throw new ValidationException("KEY debe ser base64 de 32 bytes");
                }
            } else {
                throw new ValidationException("KEY debe tener prefijo base64:");
            }
    }

    // CONFIGURACIONES POR DEFECTO (desarrollo)
    $_ENV['APP_ENV'] ??= $environment;
    $_ENV['APP_DEBUG']        ??= 'true';
    $_ENV['MAINTENANCE_MODE'] ??= 'false';
    $_ENV['CACHE_DRIVER']     ??= 'file';
    $_ENV['SESSION_DRIVER']   ??= 'file';
    $_ENV['LOG_LEVEL']        ??= 'debug';
    $_ENV['BCRYPT_ROUNDS']    ??= '12';
    $_ENV['RATE_LIMIT_REQUESTS'] ??= '60';
    $_ENV['RATE_LIMIT_MINUTES']  ??= '1';

    
    $validEnvs = ['development', 'staging', 'production'];
    if (!in_array($environment, $validEnvs, true)) {
        die("Entorno inválido. Contacte al administrador.");
    }


    // HELPER FUNCTIONS (funciones de ayuda, para validar segun tipo los valores de las variables de entorno)
    # Obtener variable de entorno con valor por defecto
    function env($key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }

    # Obtener variable booleana
    function env_bool($key, $default = false) {
        $value = env($key, $default);
        if (is_bool($value)) {
            return $value;
        }
        return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
    }

    # Obtener variable como entero
    function env_int($key, $default = 0) {
        return (int) env($key, $default);
    }

    # Verificar si está en modo mantenimiento
    function modoMantenimiento() {
        if (!env_bool('MAINTENANCE_MODE')) {
            return false;
        }
        $ipExcluido = explode(',', env('MAINTENANCE_ALLOWED_IPS', ''));
        $clienteIp   = $_SERVER['REMOTE_ADDR'] ?? '';
        return !in_array(trim($clienteIp), array_map('trim', $ipExcluido), strict: true);
    }

    # Verificar configuración de base de datos
    function testConexionDB() {
        try {
            $pdo = new PDO(
                sprintf(
                    '%s:host=%s;port=%s;dbname=%s;charset=%s',
                    env('DB_CONNECTION', 'pgsql'),
                    env('DB_HOST'),
                    env('DB_PORT', 5432),
                    env('DB_DATABASE'),
                    env('DB_CHARSET', 'utf8')
                ),
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
            return true;
        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            return false;
        }
    }

} catch (ValidationException $e) {
    error_log("Error de validación de configuración: " . $e->getMessage());
    if ($environment === 'development') {
        die("Error de configuración: " . $e->getMessage());
    }
    die("Error de configuración del sistema" . $e->getMessage());
} catch (Exception $e) {
    error_log("Error cargando configuración: " . $e->getMessage());
    die("Error crítico del sistema" . $e->getMessage());
}

# CONFIGURACIÓN EXTRA
// Establecer zona horaria (si se definió en .env)
if (env('APP_TIMEZONE')) {
    date_default_timezone_set(env('APP_TIMEZONE'));
}

// Configurar nivel de error para desarrollo local
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar configuración de sesión segura
if (env_bool('SESSION_COOKIE_SECURE')) {
    ini_set('session.cookie_secure', 1);
}
if (env('SESSION_COOKIE_HTTPONLY')) {
    ini_set('session.cookie_httponly', 1);
}
if (env('SESSION_SAME_SITE')) {
    ini_set('session.cookie_samesite', env('SESSION_SAME_SITE'));
}