<?php
// config/env.php
#declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;

try {
    // ==============================================
    // DETECCIÓN DE ENTORNO (siempre “development” por defecto)
    // ==============================================
    $environment = getenv('APP_ENV') ?: 'development';

    // En desarrollo local, cargar .env si existe
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // ==============================================
        // VALIDACIONES BÁSICAS OBLIGATORIAS (solo desarrollo)
        // ==============================================
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
    }

    // ==============================================
    // VALIDACIONES ADICIONALES DE DESARROLLO
    // ==============================================
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
        $urlFields = ['APP_URL'];
        foreach ($urlFields as $field) {
            if (!empty($_ENV[$field]) && !filter_var($_ENV[$field], FILTER_VALIDATE_URL)) {
                throw new ValidationException("$field debe ser una URL válida");
            }
        }

        // Emails válidos
        $emailFields = ['MAIL_FROM_ADDRESS', 'MAIL_USERNAME'];
        foreach ($emailFields as $field) {
            if (!empty($_ENV[$field]) && !filter_var($_ENV[$field], FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException("$field debe ser un email válido");
            }
        }

        // Validar longitud mínima de claves secretas
        $secretKeys = [
            'APP_KEY'        => 32,
            'ENCRYPTION_KEY' => 16,
            'SECRET_KEY'     => 32,
        ];
        foreach ($secretKeys as $key => $minLength) {
            if (!empty($_ENV[$key])) {
                $value = $_ENV[$key];
                if (strpos($value, 'base64:') === 0) {
                    $value = base64_decode(substr($value, 7));
                }
                if (strlen($value) < $minLength) {
                    error_log("Clave $key no cumple longitud mínima");
                    throw new ValidationException("$key debe tener al menos $minLength caracteres");
                }
            }
        }
    }

    // ==============================================
    // CONFIGURACIONES POR DEFECTO (desarrollo)
    // ==============================================
    $_ENV['APP_ENV'] ??= $environment;
    $_ENV['APP_DEBUG']        ??= 'true';
    $_ENV['MAINTENANCE_MODE'] ??= 'false';
    $_ENV['CACHE_DRIVER']     ??= 'file';
    $_ENV['SESSION_DRIVER']   ??= 'file';
    $_ENV['LOG_LEVEL']        ??= 'debug';
    $_ENV['BCRYPT_ROUNDS']    ??= '12';
    $_ENV['RATE_LIMIT_REQUESTS'] ??= '60';
    $_ENV['RATE_LIMIT_MINUTES']  ??= '1';

    // ==============================================
    // HELPER FUNCTIONS
    // ==============================================

    /**
     * Obtener variable de entorno con valor por defecto
     */
    function env($key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Obtener variable booleana
     */
    function env_bool($key, $default = false) {
        $value = env($key, $default);
        if (is_bool($value)) {
            return $value;
        }
        return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
    }

    /**
     * Obtener variable como entero
     */
    function env_int($key, $default = 0) {
        return (int) env($key, $default);
    }

    /**
     * Verificar si está en modo mantenimiento
     */
    function is_maintenance_mode() {
        if (!env_bool('MAINTENANCE_MODE')) {
            return false;
        }
        $allowedIps = explode(',', env('MAINTENANCE_ALLOWED_IPS', ''));
        $clientIp   = $_SERVER['REMOTE_ADDR'] ?? '';
        return !in_array(trim($clientIp), array_map('trim', $allowedIps), strict: true);
    }

    /**
     * Verificar configuración de base de datos
     */
    function test_database_connection() {
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

// ==============================================
// CONFIGURACIÓN FINAL
// ==============================================

// Establecer zona horaria (si se definió en .env)
if (env('APP_TIMEZONE')) {
    date_default_timezone_set(env('APP_TIMEZONE'));
}

// Configurar nivel de error para desarrollo local
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar configuración de sesión segura
if (env('SESSION_COOKIE_SECURE')) {
    ini_set('session.cookie_secure', 1);
}
if (env('SESSION_COOKIE_HTTPONLY')) {
    ini_set('session.cookie_httponly', 1);
}
if (env('SESSION_SAME_SITE')) {
    ini_set('session.cookie_samesite', env('SESSION_SAME_SITE'));
}