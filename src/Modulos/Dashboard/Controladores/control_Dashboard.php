<?php
// src/Modulos/Dashboard/Controladores/control_Dashboard.php
#declare(strict_types=1);

namespace App\Modulos\Dashboard\Controladores;

use App\Modulos\Dashboard\Modelos\ModeloDashboard;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Dashboard {
    protected ModeloDashboard $modelo;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        // Inicializar modelo y logger
        $this->modelo = new ModeloDashboard();
        $this->logger = loggers::createLogger();
        $this->logger->info("💼 control_Dashboard::__construct() inicializado");
    }

    /**
     * Despacha rutas que inicien en /dashboard
     * @param string $uri
     * @param string $method
     */
    public function handle(string $uri, string $method): void {
        $this->logger->info("🏷️  control_Dashboard::handle() invocado para: {$method} {$uri}");

        // Verificar autenticación y rol ADMIN (redundante pero seguro)
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
                $this->logger->info("↪️  GET /Dashboard → Dashboard()");
                $this->index();
                return;

            case 'GET /dashboard/funcionarios':
                $this->logger->info("↪️  GET /dashboard/funcionarios → listarFunc()");
                $this->listarFunc();
                return;

            case 'GET /dashboard/funcionarios/crear':
                $this->logger->info("↪️  GET /dashboard/funcionarios/crear → crearForm()");
                $this->crearForm();
                return;

            case 'POST /dashboard/funcionarios':
                $this->logger->info("↪️  POST /dashboard/funcionarios → crear()");
                $this->crear();
                return;

            case 'GET /dashboard/funcionarios/editar':
                $this->logger->info("↪️  GET /dashboard/funcionarios/editar → editarForm()");
                $this->editarForm();
                return;

            case 'POST /dashboard/funcionarios/editar':
                $this->logger->info("↪️  POST /dashboard/funcionarios/editar → editar()");
                $this->editar();
                return;

            case 'POST /dashboard/funcionarios/eliminar':
                $this->logger->info("↪️  POST /dashboard/funcionarios/eliminar → eliminar()");
                $this->eliminar();
                return;

            case 'POST /dashboard/funcionarios/activar':
                $this->logger->info("↪️  POST /dashboard/funcionarios/activar → activar()");
                $this->activar();
                return;

            case 'GET /dashboard/auditoria':
                $this->logger->info("↪️  GET /dashboard/auditoria → verAuditoria()");
                $this->verAuditoria();
                return;

            case 'GET /dashboard/estadisticas':
                $this->logger->info("↪️  GET /dashboard/estadisticas → estadisticas()");
                $this->estadisticas();
                return;

            default:
                $this->logger->warning("❓ Dashboard: ruta no encontrada ({$uri})");
                http_response_code(404);
                echo "Dashboard: ruta no encontrada ({$uri})";
                return;
        }
    }

    /** GET /dashboard */
    protected function index(): void {
        $this->logger->debug("🔄 index(): obteniendo resumen");
      #  $dataEst = $this->modelo->getResumen();
        $this->logger->info("✔️ index(): resumen obtenido");
        require_once __DIR__ . '/../Vistas/dashboard.php';
        $this->logger->info("📄 index(): vista dashboard cargada");
    }

    /** GET /dashboard/funcionarios */
    protected function listarFunc(): void {
        $this->logger->debug("🔄 listarFunc(): obteniendo lista de funcionarios");
        $funcionarios = $this->modelo->getAllFuncionarios();
        $this->logger->info("✔️ listarFunc(): funcionarios obtenidos (" . count($funcionarios) . ")");
        require __DIR__ . '/../vistas/dashboard.php';
        $this->logger->info("📄 listarFunc(): vista dashboard cargada con funcionarios");
    }

    /** GET /dashboard/funcionarios/crear */
    protected function crearForm(): void
    {
        $this->logger->info("🔄 crearForm(): mostrando formulario de creación");
        require __DIR__ . '/../vistas/dashboard.php';
        $this->logger->info("📄 crearForm(): vista dashboard cargada (crear funcionario)");
    }

    /** POST /dashboard/funcionarios */
    protected function crear(): void
    {
        $this->logger->debug("🔄 crear(): recolectando datos del POST", [
            'POST' => $_POST
        ]);
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
        $this->logger->info("✅ crear(): funcionario creado: {\$nombre}, {\$correo}");
        $_SESSION['dashboard_message'] = 'Funcionario creado correctamente.';
        header('Location: /dashboard/funcionarios');
        exit;
    }

    /** GET /dashboard/funcionarios/editar?id=XX */
    protected function editarForm(): void
    {
        $id = intval($_GET['id'] ?? 0);
        $this->logger->debug("🔄 editarForm(): id recibido: {\$id}");
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
        $this->logger->info("✔️ editarForm(): funcionario encontrado para ID={\$id}");
        require __DIR__ . '/../vistas/dashboard.php';
        $this->logger->info("📄 editarForm(): vista dashboard cargada (editar funcionario)");
    }

    /** POST /dashboard/funcionarios/editar */
    protected function editar(): void
    {
        $this->logger->debug("🔄 editar(): recolectando datos del POST", [
            'POST' => $_POST
        ]);
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
        $this->logger->info("✅ editar(): funcionario actualizado: ID={\$id}, \{\$nombre}, {\$correo}");
        $_SESSION['dashboard_message'] = 'Funcionario actualizado correctamente.';
        header('Location: /dashboard/funcionarios');
        exit;
    }

    /** POST /dashboard/funcionarios/eliminar */
    protected function eliminar(): void
    {
        $id = intval($_POST['id'] ?? 0);
        $this->logger->debug("🔄 eliminar(): id recibido: {\$id}");
        if ($id > 0) {
            $this->modelo->eliminarFuncionario($id);
            $this->logger->info("🗑 eliminar(): funcionario eliminado ID={\$id}");
            $_SESSION['dashboard_message'] = 'Funcionario eliminado correctamente.';
        } else {
            $this->logger->warning("⚠️ eliminar(): ID inválido (<=0)");
        }
        header('Location: /dashboard/funcionarios');
        exit;
    }

    /** POST /dashboard/funcionarios/activar */
    protected function activar(): void
    {
        $id     = intval($_POST['id'] ?? 0);
        $estado = ($_POST['estado'] ?? '0') === '1' ? 1 : 0;
        $this->logger->debug("🔄 activar(): id={\$id}, estado={\$estado}");
        if ($id > 0) {
            $this->modelo->activarFuncionario($id, $estado);
            if ($estado) {
                $this->logger->info("✅ activar(): funcionario ID={\$id} activado");
                $_SESSION['dashboard_message'] = 'Funcionario activado correctamente.';
            } else {
                $this->logger->info("🚫 activar(): funcionario ID={\$id} desactivado");
                $_SESSION['dashboard_message'] = 'Funcionario desactivado correctamente.';
            }
        } else {
            $this->logger->warning("⚠️ activar(): ID inválido (<=0)");
        }
        header('Location: /dashboard/funcionarios');
        exit;
    }

    /** GET /dashboard/auditoria */
    protected function verAuditoria(): void
    {
        $this->logger->debug("🔄 verAuditoria(): obteniendo registros de auditoría");
        $auditorias = $this->modelo->getLogAuditoria();
        $this->logger->info("✔️ verAuditoria(): auditorías obtenidas (" . count($auditorias) . ")");
        require __DIR__ . '/../vistas/dashboard.php';
        $this->logger->info("📄 verAuditoria(): vista dashboard cargada (auditoría)");
    }

    /** GET /dashboard/estadisticas */
    protected function estadisticas(): void
    {
        $this->logger->debug("🔄 estadisticas(): obteniendo datos de estadísticas");
        $data = $this->modelo->getEstadisticas();
        $this->logger->info("✔️ estadisticas(): datos obtenidos");
        require __DIR__ . '/../vistas/dashboard.php';
        $this->logger->info("📄 estadisticas(): vista dashboard cargada (estadísticas)");
    }
}
