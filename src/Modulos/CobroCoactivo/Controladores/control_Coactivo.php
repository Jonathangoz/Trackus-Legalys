<?php
# src/Modulos/CobroCoactivo/Controladores/control_Coactivo.php (controlador principal del modulo cobroCoactivo)
declare(strict_types=1);

namespace App\Modulos\CobroCoactivo\Controladores;

use App\Modulos\CobroCoactivo\Modelos\procesos;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Coactivo {
    protected procesos $modelo;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializar modelo y logger
        $this->modelo = new procesos();
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que inicien en /cobrocoactivo
     * @param string $uri
     * @param string $method
     */
    public function handle(string $uri, string $method): void {

        // Verificar autenticación y rol ADMIN_TRAMITE (redundante pero seguro)
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            $this->logger->warning("🚫 Usuario no autenticado en CobroCoactivo, redirigiendo a /login");
            header('Location: /login');
            exit;
        }
        $rol = $_SESSION['tipo_rol'] ?? null;
        $this->logger->debug("👤 Rol desde sesión en cobrocoactivo: {$rol}");
        if ($rol !== 'ADMIN') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN_TRAMITE en el Index, redirigiendo a /login");
            header('Location: /login');
            exit;
        }

        switch ("{$method} {$uri}") {
            case 'GET /cobrocoactivo':
            case 'POST /cobrocoactivo':
                $this->listadoProcesos();
                return;

            case 'GET /cobrocoactivo/funcionarios':
                $this->listarFunc();
                return;

            case 'GET /cobrocoactivo/funcionarios/crear':
                $this->crearForm();
                return;

            case 'POST /cobrocoactivo/funcionarios':
                $this->crear();
                return;

            case 'GET /cobrocoactivo/funcionarios/editar':
                $this->editarForm();
                return;

            case 'POST /cobrocoactivo/funcionarios/editar':
                $this->editar();
                return;

            case 'POST /cobrocoactivo/funcionarios/eliminar':
                $this->eliminar();
                return;

            case 'POST /cobrocoactivo/funcionarios/activar':
                $this->activar();
                return;

            case 'GET /cobrocoactivo/auditoria':
                $this->verAuditoria();
                return;

            case 'GET /cobrocoactivo/estadisticas':
                $this->estadisticas();
                return;

            default:
                $this->logger->warning("❓ cobrocoactivo: ruta no encontrada ({$uri})");
                http_response_code(404);
                echo "cobrocoactivo: ruta no encontrada ({$uri})";
                return;
        }
    }

    # GET /cobrocoactivo
    protected function listadoProcesos(): void {
        $datos = [
            'entidades' => $this->modelo->getEntidades(),
        ];
        extract($datos);
        require_once __DIR__ . '/../Vistas/procesos.php';
    }

    # GET /cobrocoactivo/funcionarios
    protected function listarFunc(): void {
        $this->modelo->getAllFuncionarios();
        require __DIR__ . '/../vistas/cobrocoactivo.php';
    }

    # GET /cobrocoactivo/funcionarios/crear
    protected function crearForm(): void {
        require __DIR__ . '/../vistas/cobrocoactivo.php';
    }

    # POST /cobrocoactivo/funcionarios
    protected function crear(): void {
        /*$this->logger->debug("🔄 crear(): recolectando datos del POST", [
            'POST' => $_POST
        ]); */
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
            $_SESSION['cobrocoactivo_errors'] = $errores;
            header('Location: /cobrocoactivo/funcionarios/crear');
            exit;
        }

        $this->modelo->insertarFuncionario($nombre, $correo, $rol, $estado);
        $_SESSION['cobrocoactivo_message'] = 'Funcionario creado correctamente.';
        header('Location: /cobrocoactivo/funcionarios');
        exit;
    }

    # GET /cobrocoactivo/funcionarios/editar?id=XX
    protected function editarForm(): void {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->logger->warning("⚠️ editarForm(): ID inválido (<=0), redirigiendo");
            header('Location: /cobrocoactivo/funcionarios');
            exit;
        }
        $func = $this->modelo->getFuncionarioById($id);
        if (!$func) {
            $this->logger->warning("⚠️ editarForm(): funcionario no encontrado para ID={\$id}");
            header('Location: /cobrocoactivo/funcionarios');
            exit;
        }
        require __DIR__ . '/../vistas/cobrocoactivo.php';
    }

    # POST /cobrocoactivo/funcionarios/editar
    protected function editar(): void {
        /*$this->logger->debug("🔄 editar(): recolectando datos del POST", [
            'POST' => $_POST
        ]); */
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
            $_SESSION['cobrocoactivo_errors'] = $errores;
            header("Location: /cobrocoactivo/funcionarios/editar?id={$id}");
            exit;
        }
        $this->modelo->actualizarFuncionario($id, $nombre, $correo, $rol, $estado);
        $_SESSION['cobrocoactivo_message'] = 'Funcionario actualizado correctamente.';
        header('Location: /cobrocoactivo/funcionarios');
        exit;
    }

    # POST /cobrocoactivo/funcionarios/eliminar
    protected function eliminar(): void {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->modelo->eliminarFuncionario($id);
            $this->logger->info("🗑 eliminar(): funcionario eliminado ID={\$id}");
            $_SESSION['cobrocoactivo_message'] = 'Funcionario eliminado correctamente.';
        } else {
            $this->logger->warning("⚠️ eliminar(): ID inválido (<=0)");
        }
        header('Location: /cobrocoactivo/funcionarios');
        exit;
    }

    # POST /cobrocoactivo/funcionarios/activar
    protected function activar(): void {
        $id     = intval($_POST['id'] ?? 0);
        $estado = ($_POST['estado'] ?? '0') === '1' ? 1 : 0;
        if ($id > 0) {
            $this->modelo->activarFuncionario($id, $estado);
            if ($estado) {
                $_SESSION['cobrocoactivo_message'] = 'Funcionario activado correctamente.';
            } else {
                $_SESSION['cobrocoactivo_message'] = 'Funcionario desactivado correctamente.';
            }
        } else {
            $this->logger->warning("⚠️ activar(): ID inválido (<=0)");
        }
        header('Location: /cobrocoactivo/funcionarios');
        exit;
    }

    # GET /cobrocoactivo/auditoria
    protected function verAuditoria(): void {
        $this->modelo->getLogAuditoria();
        require __DIR__ . '/../vistas/cobrocoactivo.php';
    }

    # GET /cobrocoactivo/estadisticas
    protected function estadisticas(): void {
        $this->modelo->getEstadisticas();
        require __DIR__ . '/../vistas/cobrocoactivo.php';
    }
}