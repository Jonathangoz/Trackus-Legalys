<?php
// public/index.php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/env.php';

use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\AbogadoController;
use App\Controllers\AdminTramiteController;
use App\Controllers\UsuarioController;
use App\Controllers\ApiController;

// ───────────────────────────────────────────────────────────────────────────────
// 1) Configuracion de vida de Sesiones

//lee desde .env
$lifetime      = intval($_ENV['SESSION_LIFETIME'] ?? 300);        // 300 s = 5 min
$idleTimeout   = intval($_ENV['SESSION_IDLE_TIMEOUT'] ?? 300);    // 300 s = 5 min

// 1.1) Establecer parámetros de la cookie de sesión:
//      - lifetime: la cookie vivirá 'lifetime' segundos, incluso si el navegador se cierra.
//      - httponly, samesite, secure como ya tenías.
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'domain'   => '',       // deja vacío o pon tu dominio
    'secure'   => false,    // pon true si usas HTTPS
    'httponly' => true,
    'samesite' => 'Strict',
]);

// 1.2) Ajustar el “garbage collector” para que limpie sesiones viejas tras 'lifetime' segundos:
ini_set('session.gc_maxlifetime', (string)$lifetime);
session_cache_expire(intval($lifetime / 60)); // en minutos


// ───────────────────────────────────────────────────────────────────────────────
// 2) Iniciar (o reanudar) sesión de forma segura
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'cookie_secure'   => false, // true si usas HTTPS
]);

// ───────────────────────────────────────────────────────────────────────────────
// 3) Control de “idle timeout”: si lleva demasiado tiempo sin actividad, destruir la sesión.
// ───────────────────────────────────────────────────────────────────────────────

// Guardamos en sesión el timestamp de la última actividad. Si no existe, es la primera vez.
$ahora = time();
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactividad = $ahora - intval($_SESSION['LAST_ACTIVITY']);
    if ($inactividad > $idleTimeout) {
        // Ya superó el tiempo de inactividad: cerramos sesión automáticamente.
        // Llamamos al logout para limpiar todo (cookie, session_destroy, headers anti-cache).
        $auth = new AuthController();
        $auth->logout();
        exit; // asegurarnos de no seguir con más código
    }
}
// Actualizamos siempre el LAST_ACTIVITY para la próxima petición
$_SESSION['LAST_ACTIVITY'] = $ahora;


// ───────────────────────────────────────────────────────────────────────────────
// 4) Parsear la ruta y el método HTTP
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// ───────────────────────────────────────────────────────────────────────────────
// 5) Ruteo centralizado
switch ($uri) {

    // ------------------------------------------------------
    // RUTAS DE AUTENTICACIÓN
    // ------------------------------------------------------

    // GET  /login   → muestra formulario de login
    // POST /login   → procesa datos de login
    case '/login':
        $auth = new AuthController();
        if ($method === 'GET') {
            $auth->showLoginForm();
        } elseif ($method === 'POST') {
            // Validar CSRF
            $csrfForm = $_POST['csrf_token'] ?? '';
            $csrfSes  = $_SESSION['csrf_token'] ?? '';
            if (! hash_equals($csrfSes, $csrfForm)) {
                http_response_code(400);
                echo "CSRF token inválido.";
                exit;
            }
            $auth->login();
        } else {
            http_response_code(405);
            echo "Método no permitido.";
        }
        break;

    // GET /logout → cierra sesión y redirige a /login
    case '/logout':
        $auth = new AuthController();
        $auth->logout();
        break;


    // ------------------------------------------------------
    // RUTAS ADMIN
    // ------------------------------------------------------

    // Ejemplos:
    // GET /admin/dashboard    → panel principal del administrador
    // GET /admin/usuarios     → CRUD de usuarios (exclusivo ADMIN)
    // GET /admin/deudores     → lista/gestiona deudores
    if (strpos($uri, '/admin') === 0) {
        $admin = new AdminController();
        // Si vinieron en GET /admin/dashboard
        if ($uri === '/admin/dashboard' && $method === 'GET') {
            $admin->dashboard();
        }
        // Si GET /admin/usuarios
        elseif ($uri === '/admin/usuarios' && $method === 'GET') {
            $admin->usuarios();
        }
        // GET /admin/deudores
        elseif ($uri === '/admin/deudores' && $method === 'GET') {
            $admin->deudores();
        }
        // Otros endpoints ADMIN: CRUD, reportes, etc.
        else {
            http_response_code(404);
            echo "Página admin no encontrada.";
        }
        break;
    }


    // ------------------------------------------------------
    // RUTAS ADMIN_TRAMITE
    // ------------------------------------------------------

    // Ejemplo: GET /admin_tramite/dashboard
    if (strpos($uri, '/admin_tramite') === 0) {
        $adminT = new AdminTramiteController();
        if ($uri === '/admin_tramite/dashboard' && $method === 'GET') {
            $adminT->dashboard();
        }
        // otras rutas para ADMIN_TRAMITE…
        else {
            http_response_code(404);
            echo "Página admin_tramite no encontrada.";
        }
        break;
    }


    // ------------------------------------------------------
    // RUTAS ABOGADO
    // ------------------------------------------------------

    // Ejemplos:
    // GET /abogado/procesos     → lista de procesos
    // GET /abogado/calendario   → calendario
    if (strpos($uri, '/abogado') === 0) {
        $abogado = new AbogadoController();
        if ($uri === '/abogado/procesos' && $method === 'GET') {
            $abogado->procesos();
        }
        elseif ($uri === '/abogado/calendario' && $method === 'GET') {
            $abogado->calendario();
        }
        // otros endpoints ABOGADO…
        else {
            http_response_code(404);
            echo "Página abogado no encontrada.";
        }
        break;
    }


    // ------------------------------------------------------
    // RUTAS USUARIO
    // ------------------------------------------------------

    // Ejemplo: GET /usuario/consultas
    if (strpos($uri, '/usuario') === 0) {
        $usuario = new UsuarioController();
        if ($uri === '/usuario/consultas' && $method === 'GET') {
            $usuario->consultas();
        }
        // otros endpoints USUARIO…
        else {
            http_response_code(404);
            echo "Página usuario no encontrada.";
        }
        break;
    }


    // ------------------------------------------------------
    // RUTA AJAX / API → POST /api
    // ------------------------------------------------------
    case '/api':
        if ($method === 'POST') {
            $api = new ApiController();
            $api->handle();
        } else {
            http_response_code(405);
            echo "Método no permitido.";
        }
        break;


    // ------------------------------------------------------
    // RUTA POR DEFECTO: 404 Not Found
    // ------------------------------------------------------
    default:
        http_response_code(404);
        echo "Página no encontrada.";
        break;
}
