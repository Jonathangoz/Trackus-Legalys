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
        $this->logger->info("💼 control_Coactivo::__construct() inicializado");
    }

    /**
     * Despacha rutas que inicien en /cobrocoactivo
     * @param string $uri
     * @param string $method
     */
    public function handle(string $uri, string $method): void {
        $this->logger->info("🏷️  control_Coactivo::handle() invocado para: {$method} {$uri}");

        // Verificar autenticación y rol ADMIN_TRAMITE (redundante pero seguro)
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            $this->logger->warning("🚫 Usuario no autenticado en CobroCoactivo, redirigiendo a /login");
            header('Location: /login');
            exit;
        }
        $rol = $_SESSION['tipo_rol'] ?? null;
        $this->logger->debug("👤 Rol desde sesión en cobrocoactivo: {$rol}");
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN_TRAMITE en el Index, redirigiendo a /login");
            header('Location: /login');
            exit;
        }

        switch ("{$method} {$uri}") {
            case 'GET /cobrocoactivo':
            case 'POST /cobrocoactivo':
                $this->logger->info("↪️  GET /cobrocoactivo → procesos()");
                $this->listadoProcesos();
                return;

            case 'GET /cobrocoactivo/funcionarios':
                $this->logger->info("↪️  GET /cobrocoactivo/funcionarios → listarFunc()");
                $this->listarFunc();
                return;

            case 'GET /cobrocoactivo/funcionarios/crear':
                $this->logger->info("↪️  GET /cobrocoactivo/funcionarios/crear → crearForm()");
                $this->crearForm();
                return;

            case 'POST /cobrocoactivo/funcionarios':
                $this->logger->info("↪️  POST /cobrocoactivo/funcionarios → crear()");
                $this->crear();
                return;

            case 'GET /cobrocoactivo/funcionarios/editar':
                $this->logger->info("↪️  GET /cobrocoactivo/funcionarios/editar → editarForm()");
                $this->editarForm();
                return;

            case 'POST /cobrocoactivo/funcionarios/editar':
                $this->logger->info("↪️  POST /cobrocoactivo/funcionarios/editar → editar()");
                $this->editar();
                return;

            case 'POST /cobrocoactivo/funcionarios/eliminar':
                $this->logger->info("↪️  POST /cobrocoactivo/funcionarios/eliminar → eliminar()");
                $this->eliminar();
                return;

            case 'POST /cobrocoactivo/funcionarios/activar':
                $this->logger->info("↪️  POST /cobrocoactivo/funcionarios/activar → activar()");
                $this->activar();
                return;

            case 'GET /cobrocoactivo/auditoria':
                $this->logger->info("↪️  GET /cobrocoactivo/auditoria → verAuditoria()");
                $this->verAuditoria();
                return;

            case 'GET /cobrocoactivo/estadisticas':
                $this->logger->info("↪️  GET /cobrocoactivo/estadisticas → estadisticas()");
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
        $this->logger->debug("🔄 listadoProcesos(): obteniendo resumen");
        $datos = [
            'entidades' => $this->modelo->getEntidades(),
        ];
        // $datos = [
        //   'entidades' => array asociativo con conteos de bancos, cc y tránsito,
        // ]
        extract($datos);
        
        $this->logger->info("✔️ listadoProcesos(): resumen obtenido");
        require_once __DIR__ . '/../Vistas/procesos.php';
        $this->logger->info("📄 listadoProcesos(): vista cobrocoactivo cargada");
    }

    # GET /cobrocoactivo/funcionarios
    protected function listarFunc(): void {
        $this->logger->debug("🔄 listarFunc(): obteniendo lista de funcionarios");
        $funcionarios = $this->modelo->getAllFuncionarios();
        $this->logger->info("✔️ listarFunc(): funcionarios obtenidos (" . count($funcionarios) . ")");
        require __DIR__ . '/../vistas/cobrocoactivo.php';
        $this->logger->info("📄 listarFunc(): vista cobrocoactivo cargada con funcionarios");
    }

    # GET /cobrocoactivo/funcionarios/crear
    protected function crearForm(): void {
        $this->logger->info("🔄 crearForm(): mostrando formulario de creación");
        require __DIR__ . '/../vistas/cobrocoactivo.php';
        $this->logger->info("📄 crearForm(): vista cobrocoactivo cargada (crear funcionario)");
    }

    # POST /cobrocoactivo/funcionarios
    protected function crear(): void {
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
            $_SESSION['cobrocoactivo_errors'] = $errores;
            header('Location: /cobrocoactivo/funcionarios/crear');
            exit;
        }

        $this->modelo->insertarFuncionario($nombre, $correo, $rol, $estado);
        $this->logger->info("✅ crear(): funcionario creado: {\$nombre}, {\$correo}");
        $_SESSION['cobrocoactivo_message'] = 'Funcionario creado correctamente.';
        header('Location: /cobrocoactivo/funcionarios');
        exit;
    }

    # GET /cobrocoactivo/funcionarios/editar?id=XX
    protected function editarForm(): void {
        $id = intval($_GET['id'] ?? 0);
        $this->logger->debug("🔄 editarForm(): id recibido: {\$id}");
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
        $this->logger->info("✔️ editarForm(): funcionario encontrado para ID={\$id}");
        require __DIR__ . '/../vistas/cobrocoactivo.php';
        $this->logger->info("📄 editarForm(): vista cobrocoactivo cargada (editar funcionario)");
    }

    # POST /cobrocoactivo/funcionarios/editar
    protected function editar(): void {
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
            $_SESSION['cobrocoactivo_errors'] = $errores;
            header("Location: /cobrocoactivo/funcionarios/editar?id={$id}");
            exit;
        }

        $this->modelo->actualizarFuncionario($id, $nombre, $correo, $rol, $estado);
        $this->logger->info("✅ editar(): funcionario actualizado: ID={\$id}, \{\$nombre}, {\$correo}");
        $_SESSION['cobrocoactivo_message'] = 'Funcionario actualizado correctamente.';
        header('Location: /cobrocoactivo/funcionarios');
        exit;
    }

    # POST /cobrocoactivo/funcionarios/eliminar
    protected function eliminar(): void {
        $id = intval($_POST['id'] ?? 0);
        $this->logger->debug("🔄 eliminar(): id recibido: {\$id}");
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
        $this->logger->debug("🔄 activar(): id={\$id}, estado={\$estado}");
        if ($id > 0) {
            $this->modelo->activarFuncionario($id, $estado);
            if ($estado) {
                $this->logger->info("✅ activar(): funcionario ID={\$id} activado");
                $_SESSION['cobrocoactivo_message'] = 'Funcionario activado correctamente.';
            } else {
                $this->logger->info("🚫 activar(): funcionario ID={\$id} desactivado");
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
        $this->logger->debug("🔄 verAuditoria(): obteniendo registros de auditoría");
        $auditorias = $this->modelo->getLogAuditoria();
        $this->logger->info("✔️ verAuditoria(): auditorías obtenidas (" . count($auditorias) . ")");
        require __DIR__ . '/../vistas/cobrocoactivo.php';
        $this->logger->info("📄 verAuditoria(): vista cobrocoactivo cargada (auditoría)");
    }

    # GET /cobrocoactivo/estadisticas
    protected function estadisticas(): void {
        $this->logger->debug("🔄 estadisticas(): obteniendo datos de estadísticas");
        $data = $this->modelo->getEstadisticas();
        $this->logger->info("✔️ estadisticas(): datos obtenidos");
        require __DIR__ . '/../vistas/cobrocoactivo.php';
        $this->logger->info("📄 estadisticas(): vista cobrocoactivo cargada (estadísticas)");
    }
}