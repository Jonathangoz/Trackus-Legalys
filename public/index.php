<?php
# public/index.php (Front-Controller - Controlador principal con logging exhaustivo)
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php'; //(Ruta Principal llamados de src a use App\...)
require_once __DIR__ . '/../config/env.php'; //(Ruta que valida parametros del .ENV)

use App\Comunes\utilidades\loggers;
use App\Comunes\middleware\control_logging;

# Crear instancia global de Logger
$logger = loggers::createLogger();
$logger->info("▶▶▶ METHOD: {$_SERVER['REQUEST_METHOD']}");
$logger->info("▶▶▶ URI:    {$_SERVER['REQUEST_URI']}");

# Registrar handlers de errores y excepciones para que todo vaya a Monolog
set_error_handler(function (int $severity, string $message, string $file, int $line) use ($logger) {
    $logger->error("🛑 Error PHP: {$message} en {$file}:{$line}", [
        'severity' => $severity
    ]);
});

set_exception_handler(function (\Throwable $exception) use ($logger) {
    $logger->critical("🔥 Excepción no capturada: " . $exception->getMessage(), [
        'archivo' => $exception->getFile(),
        'línea'   => $exception->getLine(),
        'trace'   => $exception->getTraceAsString()
    ]);
});

# Configuración de duración de sesiones  (de .env)
$lifetime    = intval($_ENV['SESSION_LIFETIME']      ?? 600); // segundos
$idleTimeout = intval($_ENV['SESSION_IDLE_TIMEOUT']  ?? 300); // segundos

# Definir params de la cookie de sesión (debe ir ANTES de session_start())
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'domain'   => '',       // vacío = localhost / dominio actual
    'secure'   => false,    // en desarrollo suele ser false (si no hay HTTPS)
    'httponly' => true,
    'samesite' => 'Lax',    // ó 'Strict' en producción HTTPS
]);

ini_set('session.gc_maxlifetime',   (string)$lifetime);
session_cache_expire(intval($lifetime / 60));

# Iniciar o reanudar sesión
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure' => false  // ya viene de set_cookie_params
    ]);
} else {
    echo "Sesión ya activa, reanudando...";
}

# Regenerar ID de sesión
session_regenerate_id(true);

#  Evita que las páginas queden en caché
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

# Control de "idle timeout" (destruir sesión si inactivo)
$enTiempoReal = time();
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactividad = $enTiempoReal - intval($_SESSION['LAST_ACTIVITY']);
    if ($inactividad > $idleTimeout) {
        $auth = new control_logging();
        $auth->logout();
        exit;
    }
} else {
    echo "No existe LAST_ACTIVITY en sesión. Será la primera petición.";
}

$_SESSION['LAST_ACTIVITY'] = $enTiempoReal;

# Parsear la ruta y el método HTTP
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

# Crear instancia del controlador de logging
$auth = new control_logging();

# Ruteo centralizado
switch ($uri) {
    // RUTAS DE AUTENTICACIÓN
    # GET  /login   → muestra formulario de login
    # POST /login   → procesa datos de login
    case '/login':
        $logger->info("🏷️  Ruta coincidida: /login");

        if ($method === 'GET') {
            $auth->vistaLogging();
        } elseif ($method === 'POST') {
            # Validar CSRF en formulario y sesion
            $csrfForm = $_POST['csrf_token'] ?? '';
            $csrfSes  = $_SESSION['csrf_token'] ?? '';
            if (!hash_equals($csrfSes, $csrfForm)) {
                $logger->warning("🚫 Token CSRF inválido");
                $_SESSION['login_errors'] = ['general' => 'Error en el sistema, intenta nuevamente'];
                header('Location: /login');
                exit;
            }
            $logger->info("🔐 CSRF válido. Procediendo con autenticación.");

            # El método login() ahora maneja el redirect automáticamente
            $auth->login();
        } else {
            http_response_code(405);
            echo "Método no permitido.";
        }
        break;
    
    # GET /logout → cierra sesión y redirige a /login
    case '/logout':
        $logger->info("🏷️  Ruta coincidida: /logout");
        $auth->logout();
        break;

    # Ruta raíz - redirigir al login si no está autenticado
    case '/':
    case '':
        $logger->info("🏷️  Ruta raíz accedida");
        # Si no está autenticado, va al login
        # Si está autenticado, el handleAuthenticatedRequest lo manejará
        if (!isset($_SESSION['auth_token']) || empty($_SESSION['auth_token'])) {
            header('Location: /login');
            exit;
        }
        # Si está autenticado, delegar al manejador
        $auth->handleAuthenticatedRequest($uri, $method);
        break;

    // RUTAS PARA USUARIOS AUTENTICADOS
    # Todas las demás rutas requieren autenticación
    default:
        $logger->info("🏷️  Ruta para usuario autenticado: {$uri}");
        
        # Verificar si es una ruta conocida del sistema
        $rutasConocidas = [
            '/asignacion', '/registros', '/crearcasos',  // ADMIN_TRAMITE
            '/dashboard',                                 // ADMIN
            '/deudores',                                 // ABOGADO
            '/consultas'                                 // DEUDOR
        ];
        
        $rutaConocida = false;
        foreach ($rutasConocidas as $ruta) {
            if (strpos($uri, $ruta) === 0) {
                $rutaConocida = true;
                break;
            }
        }
        
        if ($rutaConocida) {
            # Delegar al manejador de usuarios autenticados
            $auth->handleAuthenticatedRequest($uri, $method);
        } else {
            # Ruta no reconocida
            $logger->warning("❓ Ruta no reconocida: {$uri}");
            http_response_code(404);
            echo "❓ Página no encontrada";
            # Opcionalmente redirigir al login o a una página de error
            # header('Location: /login');
        }
        break;
}