<?php
# src/Modulos/Dashboard/Controladores/control_Dashboard.php
declare(strict_types=1);

namespace App\Modulos\Dashboard\Controladores;

use App\Modulos\Dashboard\Modelos\ModeloDashboard;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Dashboard {
    protected ModeloDashboard $modelo;
    /**
    * @var Logger
    */
    private Logger $logger;

    public function __construct() {
        # Inicializar modelo y logger
        $this->modelo = new ModeloDashboard();
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que inicien en /dashboard
     * @param string $uri
     * @param string $method
    */
    public function handle(string $uri, string $method): void {

        # Verificar autenticación y rol ADMIN (redundante pero seguro)
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            $this->logger->warning("🚫 Usuario no autenticado en Dashboard, redirigiendo a /login");
            header('Location: /login');
            exit;
        }
        $rol = $_SESSION['tipo_rol'] ?? null;
        $this->logger->debug("👤 Rol desde sesión en Dashboard: {$rol}");
        if ($rol !== 'ADMIN') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN en Dashboard, redirigiendo a /login");
            header('Location: /login');
            exit;
        }

        switch ("{$method} {$uri}") {
            case 'GET /dashboard':
            case 'POST /dashboard':
                $this->index();
                return;

            case 'GET /dashboard/funcionarios':
                $this->listarFunc();
                return;

            case 'GET /dashboard/funcionarios/crear':
                $this->crearForm();
                return;

            case 'POST /dashboard/funcionarios':
                $this->crear();
                return;

            case 'GET /dashboard/funcionarios/editar':
                $this->editarForm();
                return;

            case 'POST /dashboard/funcionarios/editar':
                $this->editar();
                return;

            case 'POST /dashboard/funcionarios/eliminar':
                $this->eliminar();
                return;

            case 'POST /dashboard/funcionarios/activar':
                $this->activar();
                return;

            case 'GET /dashboard/auditoria':
                $this->verAuditoria();
                return;

            case 'GET /dashboard/estadisticas':
                $this->estadisticas();
                return;

            default:
                $this->logger->warning("❓ Dashboard: ruta no encontrada ({$uri})");
                http_response_code(404);
                echo "Dashboard: ruta no encontrada ({$uri})";
                return;
        }
    }

    # GET /dashboard
    protected function index(): void {
        $datos = [
            'entidades' => $this->modelo->getEntidades(),
        ];
        extract($datos);
        require_once __DIR__ . '/../Vistas/dashboard.php';
    }

    # GET /dashboard/funcionarios
    protected function listarFunc(): void {
        $this->modelo->getAllFuncionarios();
        require __DIR__ . '/../vistas/dashboard.php';
    }

    # GET /dashboard/funcionarios/crear
    protected function crearForm(): void {
        require __DIR__ . '/../vistas/dashboard.php';
    }

    # POST /dashboard/funcionarios
    protected function crear(): void {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $rol    = trim($_POST['rol'] ?? '');
        $estado = isset($_POST['estado']) ? 1 : 0;

        $errores = [];
        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido.';
        }
        if ($rol === '') {
            $errores[] = 'El rol es obligatorio.';
        }

        if (!empty($errores)) {
            $this->logger->warning("⚠️ crear(): errores de validación", ['errores' => $errores]);
            $_SESSION['dashboard_errors'] = $errores;
            header('Location: /dashboard/funcionarios/crear');
            exit;
        }

        $this->modelo->insertarFuncionario($nombre, $correo, $rol, $estado);
        $_SESSION['dashboard_message'] = 'Funcionario creado correctamente.';
        header('Location: /dashboard/funcionarios');
        exit;
    }

    # GET /dashboard/funcionarios/editar?id=XX
    protected function editarForm(): void {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->logger->warning("⚠️ editarForm(): ID inválido (<=0), redirigiendo");
            header('Location: /dashboard/funcionarios');
            exit;
        }
        $func = $this->modelo->getFuncionarioById($id);
        if (!$func) {
            $this->logger->warning("⚠️ editarForm(): funcionario no encontrado para ID={\$id}");
            header('Location: /dashboard/funcionarios');
            exit;
        }
        require __DIR__ . '/../vistas/dashboard.php';
    }

    # POST /dashboard/funcionarios/editar
    protected function editar(): void {

        $id     = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $rol    = trim($_POST['rol'] ?? '');
        $estado = isset($_POST['estado']) ? 1 : 0;

        $errores = [];
        if ($id <= 0) {
            $errores[] = 'ID de funcionario inválido.';
        }
        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido.';
        }
        if ($rol === '') {
            $errores[] = 'El rol es obligatorio.';
        }

        if (!empty($errores)) {
            $this->logger->warning("⚠️ editar(): errores de validación", ['errores' => $errores]);
            $_SESSION['dashboard_errors'] = $errores;
            header("Location: /dashboard/funcionarios/editar?id={$id}");
            exit;
        }

        $this->modelo->actualizarFuncionario($id, $nombre, $correo, $rol, $estado);
        $_SESSION['dashboard_message'] = 'Funcionario actualizado correctamente.';
        header('Location: /dashboard/funcionarios');
        exit;
    }

    # POST /dashboard/funcionarios/eliminar
    protected function eliminar(): void {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->modelo->eliminarFuncionario($id);
            $_SESSION['dashboard_message'] = 'Funcionario eliminado correctamente.';
        } else {
            $this->logger->warning("⚠️ eliminar(): ID inválido (<=0)");
        }
        header('Location: /dashboard/funcionarios');
        exit;
    }

    # POST /dashboard/funcionarios/activar
    protected function activar(): void {
        $id     = intval($_POST['id'] ?? 0);
        $estado = ($_POST['estado'] ?? '0') === '1' ? 1 : 0;
        if ($id > 0) {
            $this->modelo->activarFuncionario($id, $estado);
            if ($estado) {
                $_SESSION['dashboard_message'] = 'Funcionario activado correctamente.';
            } else {
                $_SESSION['dashboard_message'] = 'Funcionario desactivado correctamente.';
            }
        } else {
            $this->logger->warning("⚠️ activar(): ID inválido (<=0)");
        }
        header('Location: /dashboard/funcionarios');
        exit;
    }

    # GET /dashboard/auditoria
    protected function verAuditoria(): void {
        $this->modelo->getLogAuditoria();
        require __DIR__ . '/../vistas/dashboard.php';
    }

    # GET /dashboard/estadisticas
    protected function estadisticas(): void {
        $this->modelo->getEstadisticas();
        require __DIR__ . '/../vistas/dashboard.php';
    }
}