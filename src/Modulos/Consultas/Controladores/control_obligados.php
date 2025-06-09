<?php
# src/Modulos/Consultas/Controladores/control_obligados.php
declare(strict_types=1);

namespace App\Modulos\Consultas\Controladores;

use App\Modulos\consultas\Modelos\obligadosPagos;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_obligados {
    protected obligadosPagos $modelo;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        // Inicializar modelo y logger
        $this->modelo = new obligadosPagos();
        $this->logger = loggers::createLogger();
        $this->logger->info("💼 control_Coactivo::__construct() inicializado");
    }

    /**
     * Despacha rutas que inicien en /obligados
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
        $this->logger->debug("👤 Rol desde sesión en obligados: {$rol}");
        if ($rol !== 'DEUDOR') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN_TRAMITE en el Index, redirigiendo a /login");
            header('Location: /login');
            exit;
        }

        switch ("{$method} {$uri}") {
            case 'GET /consultas':
            case 'POST /consultas':
                $this->logger->info("↪️  GET /consultas → obligadospagos()");
                $this->consultar();
                return;

            case 'GET /obligados/funcionarios':
                $this->logger->info("↪️  GET /obligados/funcionarios → listarFunc()");
                $this->listarFunc();
                return;

            case 'GET /obligados/funcionarios/crear':
                $this->logger->info("↪️  GET /obligados/funcionarios/crear → crearForm()");
                $this->crearForm();
                return;

            case 'POST /obligados/funcionarios':
                $this->logger->info("↪️  POST /obligados/funcionarios → crear()");
                $this->crear();
                return;

            case 'GET /obligados/funcionarios/editar':
                $this->logger->info("↪️  GET /obligados/funcionarios/editar → editarForm()");
                $this->editarForm();
                return;

            case 'POST /obligados/funcionarios/editar':
                $this->logger->info("↪️  POST /obligados/funcionarios/editar → editar()");
                $this->editar();
                return;

            case 'POST /obligados/funcionarios/eliminar':
                $this->logger->info("↪️  POST /obligados/funcionarios/eliminar → eliminar()");
                $this->eliminar();
                return;

            case 'POST /obligados/funcionarios/activar':
                $this->logger->info("↪️  POST /obligados/funcionarios/activar → activar()");
                $this->activar();
                return;

            case 'GET /obligados/auditoria':
                $this->logger->info("↪️  GET /obligados/auditoria → verAuditoria()");
                $this->verAuditoria();
                return;

            case 'GET /obligados/estadisticas':
                $this->logger->info("↪️  GET /obligados/estadisticas → estadisticas()");
                $this->estadisticas();
                return;

            default:
                $this->logger->warning("❓ obligados: ruta no encontrada ({$uri})");
                http_response_code(404);
                echo "obligados: ruta no encontrada ({$uri})";
                return;
        }
    }

    # GET /obligados
    protected function consultar(): void {
        $this->logger->debug("🔄 listadoProcesos(): obteniendo resumen");
        $datos = [
            'entidades' => $this->modelo->getEntidades(),
        ];
        // $datos = [
        //   'entidades' => array asociativo con conteos de bancos, cc y tránsito,
        // ]
        extract($datos);
        
        $this->logger->info("✔️ listadoProcesos(): resumen obtenido");
        require_once __DIR__ . '/../Vistas/Consultas.php';
        $this->logger->info("📄 listadoProcesos(): vista obligados cargada");
    }

    # GET /obligados/funcionarios
    protected function listarFunc(): void {
        $this->logger->debug("🔄 listarFunc(): obteniendo lista de funcionarios");
        $funcionarios = $this->modelo->getAllFuncionarios();
        $this->logger->info("✔️ listarFunc(): funcionarios obtenidos (" . count($funcionarios) . ")");
        require __DIR__ . '/../vistas/obligados.php';
        $this->logger->info("📄 listarFunc(): vista obligados cargada con funcionarios");
    }

    # GET /obligados/funcionarios/crear
    protected function crearForm(): void {
        $this->logger->info("🔄 crearForm(): mostrando formulario de creación");
        require __DIR__ . '/../vistas/obligados.php';
        $this->logger->info("📄 crearForm(): vista obligados cargada (crear funcionario)");
    }

    # POST /obligados/funcionarios
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
            $_SESSION['obligados_errors'] = $errores;
            header('Location: /obligados/funcionarios/crear');
            exit;
        }

        $this->modelo->insertarFuncionario($nombre, $correo, $rol, $estado);
        $this->logger->info("✅ crear(): funcionario creado: {\$nombre}, {\$correo}");
        $_SESSION['obligados_message'] = 'Funcionario creado correctamente.';
        header('Location: /obligados/funcionarios');
        exit;
    }

    # GET /obligados/funcionarios/editar?id=XX
    protected function editarForm(): void {
        $id = intval($_GET['id'] ?? 0);
        $this->logger->debug("🔄 editarForm(): id recibido: {\$id}");
        if ($id <= 0) {
            $this->logger->warning("⚠️ editarForm(): ID inválido (<=0), redirigiendo");
            header('Location: /obligados/funcionarios');
            exit;
        }
        $func = $this->modelo->getFuncionarioById($id);
        if (!$func) {
            $this->logger->warning("⚠️ editarForm(): funcionario no encontrado para ID={\$id}");
            header('Location: /obligados/funcionarios');
            exit;
        }
        $this->logger->info("✔️ editarForm(): funcionario encontrado para ID={\$id}");
        require __DIR__ . '/../vistas/obligados.php';
        $this->logger->info("📄 editarForm(): vista obligados cargada (editar funcionario)");
    }

    # POST /obligados/funcionarios/editar
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
            $_SESSION['obligados_errors'] = $errores;
            header("Location: /obligados/funcionarios/editar?id={$id}");
            exit;
        }

        $this->modelo->actualizarFuncionario($id, $nombre, $correo, $rol, $estado);
        $this->logger->info("✅ editar(): funcionario actualizado: ID={\$id}, \{\$nombre}, {\$correo}");
        $_SESSION['obligados_message'] = 'Funcionario actualizado correctamente.';
        header('Location: /obligados/funcionarios');
        exit;
    }

    # POST /obligados/funcionarios/eliminar
    protected function eliminar(): void {
        $id = intval($_POST['id'] ?? 0);
        $this->logger->debug("🔄 eliminar(): id recibido: {\$id}");
        if ($id > 0) {
            $this->modelo->eliminarFuncionario($id);
            $this->logger->info("🗑 eliminar(): funcionario eliminado ID={\$id}");
            $_SESSION['obligados_message'] = 'Funcionario eliminado correctamente.';
        } else {
            $this->logger->warning("⚠️ eliminar(): ID inválido (<=0)");
        }
        header('Location: /obligados/funcionarios');
        exit;
    }

    # POST /obligados/funcionarios/activar
    protected function activar(): void {
        $id     = intval($_POST['id'] ?? 0);
        $estado = ($_POST['estado'] ?? '0') === '1' ? 1 : 0;
        $this->logger->debug("🔄 activar(): id={\$id}, estado={\$estado}");
        if ($id > 0) {
            $this->modelo->activarFuncionario($id, $estado);
            if ($estado) {
                $this->logger->info("✅ activar(): funcionario ID={\$id} activado");
                $_SESSION['obligados_message'] = 'Funcionario activado correctamente.';
            } else {
                $this->logger->info("🚫 activar(): funcionario ID={\$id} desactivado");
                $_SESSION['obligados_message'] = 'Funcionario desactivado correctamente.';
            }
        } else {
            $this->logger->warning("⚠️ activar(): ID inválido (<=0)");
        }
        header('Location: /obligados/funcionarios');
        exit;
    }

    # GET /obligados/auditoria
    protected function verAuditoria(): void {
        $this->logger->debug("🔄 verAuditoria(): obteniendo registros de auditoría");
        $auditorias = $this->modelo->getLogAuditoria();
        $this->logger->info("✔️ verAuditoria(): auditorías obtenidas (" . count($auditorias) . ")");
        require __DIR__ . '/../vistas/obligados.php';
        $this->logger->info("📄 verAuditoria(): vista obligados cargada (auditoría)");
    }

    # GET /obligados/estadisticas
    protected function estadisticas(): void {
        $this->logger->debug("🔄 estadisticas(): obteniendo datos de estadísticas");
        $data = $this->modelo->getEstadisticas();
        $this->logger->info("✔️ estadisticas(): datos obtenidos");
        require __DIR__ . '/../vistas/obligados.php';
        $this->logger->info("📄 estadisticas(): vista obligados cargada (estadísticas)");
    }
}