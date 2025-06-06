<?php
// public/index.php (Controlador principal con logging exhaustivo)

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/env.php';

use App\Comunes\utilidades\loggers;
use App\Comunes\middleware\control_logging;
use App\Comunes\seguridad\autenticacion;

// ───────────────────────────────────────────────────────────────────────────────
// 0) Crear instancia global de Logger
// ───────────────────────────────────────────────────────────────────────────────
$logger = loggers::createLogger();
$logger->info("⏩ Nueva petición recibida: {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']}");

// 0.1) Registrar handlers de errores y excepciones para que todo vaya a Monolog
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

// ───────────────────────────────────────────────────────────────────────────────
// 1) Configuración de duración de sesiones  (de .env)
// ───────────────────────────────────────────────────────────────────────────────
$lifetime    = intval($_ENV['SESSION_LIFETIME']      ?? 300); // segundos
$idleTimeout = intval($_ENV['SESSION_IDLE_TIMEOUT']  ?? 300); // segundos

$logger->debug("Configurando sesiones: lifetime={$lifetime}, idleTimeout={$idleTimeout}");

// 1.1) Definir params de la cookie de sesión (debe ir ANTES de session_start())
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

$logger->debug("session_set_cookie_params configurada y GC ajustado");

// ───────────────────────────────────────────────────────────────────────────────
// 2) Iniciar o reanudar sesión
// ───────────────────────────────────────────────────────────────────────────────
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        // 'cookie_secure' => false  // ya viene de set_cookie_params
    ]);
    $logger->info("➰ session_start() invocado, session_id(): " . session_id());
} else {
    $logger->info("💡 La sesión ya estaba activa, session_id(): " . session_id());
}

// ───────────────────────────────────────────────────────────────────────────────
// 3) Control de “idle timeout” (destruir sesión si inactivo)
// ───────────────────────────────────────────────────────────────────────────────
$now = time();
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactividad = $now - intval($_SESSION['LAST_ACTIVITY']);
    $logger->debug("Última actividad: {$_SESSION['LAST_ACTIVITY']} (hace {$inactividad}s)");

    if ($inactividad > $idleTimeout) {
        $logger->warning("🔒 Idle timeout excedido ({$inactividad}s > {$idleTimeout}s). Cerrando sesión automáticamente.");
        $auth = new control_logging();
        $auth->logout();
        $logger->info("La petición ha finalizado tras logout por idle.");
        exit;
    }
} else {
    $logger->debug("No existe LAST_ACTIVITY en sesión. Será la primera petición.");
}
$_SESSION['LAST_ACTIVITY'] = $now;
$logger->debug("Se actualiza LAST_ACTIVITY a {$now}");

// ───────────────────────────────────────────────────────────────────────────────
// 4) Parsear la ruta y el método HTTP
// ───────────────────────────────────────────────────────────────────────────────
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$logger->debug("Routing: URI='{$uri}', METHOD='{$method}'");

// ───────────────────────────────────────────────────────────────────────────────
// 5) Ruteo centralizado
// ───────────────────────────────────────────────────────────────────────────────
switch ($uri) {

    // ------------------------------------------------------
    // RUTAS DE AUTENTICACIÓN
    // ------------------------------------------------------

    // GET  /login   → muestra formulario de login
    // POST /login   → procesa datos de login
    case '/login':
        $logger->info("🏷️  Ruta coincidida: /login");
        $auth = new control_logging();

        if ($method === 'GET') {
            $logger->debug("CALL: control_logging::vistaLogging()");
            $auth->vistaLogging();
            $logger->info("✔️  Vista de login mostrada");
            exit;
        }
        elseif ($method === 'POST') {
            $logger->debug("POST en /login. Datos recibidos:", [
                'POST'          => $_POST,
                'session_token' => $_SESSION['csrf_token'] ?? null
            ]);

            // --- 5.1) Validar CSRF ---
            $csrfForm = $_POST['csrf_token'] ?? '';
            $csrfSes  = $_SESSION['csrf_token'] ?? '';
            if (!hash_equals($csrfSes, $csrfForm)) {
                $logger->warning("⚠️  CSRF inválido: session='{$csrfSes}', post='{$csrfForm}'");
                http_response_code(400);
                echo "CSRF token inválido";
                exit;
            }
            $logger->info("🔐 CSRF válido. Procediendo con autenticación.");

            // --- 5.2) Intentar login ---
            // Suponemos que control_logging::login() invoca autenticacion::login()
            $auth->login();
            if (! isset($_SESSION['user_id'])) {
                $logger->warning("❌ Login fallido para correo: " . ($_POST['correo'] ?? '(sin correo)'));
                // Puedes redirigir a /login con mensaje de error
                $_SESSION['login_errors'] = ['general' => 'Credenciales inválidas.'];
                header('Location: /login');
                exit;
            }
            $logger->info("✅ Login exitoso para correo: " . $_POST['correo']);
        
    } else {
            $logger->warning("⚠️  /login invocado con método inválido: {$method}");
            http_response_code(405);
            echo "Método no permitido.";
            exit;
        }


    // GET /logout → cierra sesión y redirige a /login
    case '/logout':
        $logger->info("🔒 Ruta /logout invocada. Cerrando sesión.");
        $auth = new control_logging();
        $auth->logout();
        $logger->info("✔️  logout() completado");
        exit;


    // ------------------------------------------------------
    // RUTA POR DEFECTO: 404 Not Found
    // ------------------------------------------------------
    default:
        $logger->warning("❓ Ruta no encontrada: {$uri}");
        http_response_code(404);
        echo "Página no encontrada.";
        exit;
}
