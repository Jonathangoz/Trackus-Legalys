<?php
# src/Modulos/Deudores/Controladores/control_Coactivo.php (controlador principal del modulo Deudores - Procesos de Cobro Coactivo)
declare(strict_types=1);

namespace App\Modulos\Deudores\Controladores;

use App\Modulos\Controladores\controlador_base;
use App\Modulos\Deudores\Modelos\formulario;
use App\Modulos\Deudores\Modelos\abogados;
use App\Comunes\seguridad\autenticacion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Coactivo extends controlador_base {
    protected abogados $modeloAbogados;
    protected formulario $modeloFormulario;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializar modelo y logger
        $this->modeloAbogados = new abogados();
        $this->modeloFormulario = new formulario();
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que inicien en /cobrocoactivo
     * @param string $uri
     * @param string $method
     */
    public function handle(string $uri, string $method): void {
        # Para evitar distinciones de mayúsculas/minúsculas:
        $path = strtolower($uri);
        $this->logger->info("🏷️  control_coactivo::handle() invocado para: {$method} {$path}");

        # Revisar si esta logueado y tiene el rol correcto
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado en Coactivo, redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # Revisar el rol del usuario
        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ABOGADO') {
            $this->logger->warning("🚫 Usuario sin rol ABOGADO en Coactivo. Rol actual: {$rol}, redirigiendo a /login");
            $this->redirect('/login');
            return;
        }

        #Redirige al modelo segun metodo, uri y lo redirecciona a la funcion principal del modelo, recorriendo lo necesario.
        $rutas = "{$method} " . strtolower(rtrim($uri, '/'));
        switch ($rutas) {

            case 'GET /cobrocoactivo':
            case 'POST /cobrocoactivo':
                $this->procesosAbogados();
                $this->logger->info("📝 Mostrando Procesos de abogados");
                return;

            case 'GET /cobrocoactivo/formularios':
            case 'POST /cobrocoactivo/formularios':
                $this->listarForm();
                return;

        default:
            $this->logger->warning("❓ cobrocoactivo::handle(): ruta no encontrada ({$uri})");
            http_response_code(404);
            echo "Ruta no encontrada: {$uri}";
            return;
        }
    }

    # GET /cobrocoactivo
    protected function procesosAbogados(): void {
        $datosAbog = [
            'abogados' => $this->modeloAbogados->getAbogados(),
        ];
        extract($datosAbog);
        require_once __DIR__ . '/../Vistas/abogado.php';
    }

    # GET /cobrocoactivo/formularios
    protected function listarForm(): void {
        $datosForm = [
            'formularios' => $this->modeloFormulario->getAllFuncionarios(),
        ];
        extract($datosForm);
        require_once __DIR__ . '/../vistas/form_abogado.php';
    }

    # POST /cobrocoactivo/funcionarios
   /* protected function crear(): void {
        /*$this->logger->debug("🔄 crear(): recolectando datos del POST", [
            'POST' => $_POST
        ]); */
    /*    $nombre = trim($_POST['nombre'] ?? '');
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
    /*    $id     = intval($_POST['id'] ?? 0);
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
    } */
}