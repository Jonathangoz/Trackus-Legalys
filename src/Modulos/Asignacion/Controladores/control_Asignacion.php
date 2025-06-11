<?php
// src/Modulos/Asignacion/Controladores/control_Asignacion.php (controlador del modelo y vistas)
declare(strict_types=1);

namespace App\Modulos\Asignacion\Controladores;

use App\Modulos\Asignacion\Modelos\asignacion;
use App\Modulos\Asignacion\Modelos\casos;
use App\Modulos\Asignacion\Modelos\registros;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Asignacion {
    protected asignacion $modeloAsignacion;
    protected casos $modeloCasos;
    protected registros $modeloRegistro;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializar modelo y logger
        $this->modeloAsignacion = new asignacion();
        $this->modeloCasos = new casos();
        $this->modeloRegistro = new registros();
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que inicien en /asignacion
     * @param string $uri
     * @param string $method
     */
    public function handle(string $uri, string $method): void {
        # Para evitar distinciones de mayúsculas/minúsculas:
        $path = strtolower($uri);
        $this->logger->info("🏷️  control_asignacion::handle() invocado para: {$method} {$path}");

        # Verificar autenticación y rol ADMIN_TRAMITE (redundante pero seguro)
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            $this->logger->warning("🚫 Usuario no autenticado en Asignacion, redirigiendo a /login");
            header('Location: /login');
            exit;
        }
        $rol = $_SESSION['tipo_rol'] ?? null;
        $this->logger->debug("👤 Rol desde sesión en asignacion: {$rol}");
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN_TRAMITE en el Index, redirigiendo a /login");
            header('Location: /login');
            exit;
        }

        #Redirige al modelo segun metodo, uri y lo redirecciona a la funcion principal del modelo, recorriendo lo necesario.
        switch ("{$method} {$uri}") {
           
            case 'GET /asignacion':
            case 'POST /asignacion':
            case 'PUT /asignacion':
                $this->logger->debug("▶ Entramos en handle(): METHOD='{$method}', URI='{$uri}'");
                if ($method === 'GET') {
                    $this->listadoAsignacion();
                } elseif ($method === 'POST') {
                    // Aquí podrías manejar un POST si fuera necesario
                    $this->logger->info("POST /asignacion no implementado, redirigiendo a listado");
                    $this->crearCasos();
                } elseif ($method === 'PUT') {
                    // Aquí podrías manejar un PUT si fuera necesario
                    $this->logger->info("PUT /asignacion no implementado, redirigiendo a listado");
                    $this->actualizarCasos();
                }
                return;

            case 'GET /asigancion/crearcasos':
            case 'POST /asigancion/crearcasos':
                if ($method === 'GET') {
                    $this->mostrarFormularioCrearCaso();
                } else { // POST
                    $this->crearCaso();
                }
                return;

            case 'GET /asignacion/registros':
            case 'POST /asignacion/registros':
                $this->logger->info("↪️  {$method} /asignacion/registros → listarRegistros() o crearRegistros()");
                if ($method === 'GET') {
                    $this->listarRegistros();
                } else { // POST
                    $this->crearRegistros();
                }
                 $this->logger->debug("▶ Entramos en handle(): METHOD='{$method}', URI='{$uri}'");
                return;

            default:
                $this->logger->warning("❓ asignacion: ruta no encontrada ({$uri})");
                http_response_code(404);
                echo "asignacion: ruta no encontrada ({$uri})";
                return;
        }
    }

    # DIRIGE AL MODELO Y CONSULTAS ESPECIFICAS

    # GET /asignacion
    protected function listadoAsignacion(): void {
        $datos = [
            'abogados' => $this->modeloAsignacion->getAbogados(),
        ];

        extract($datos);
        require_once __DIR__ . '/../Vistas/asignacion.php';
    }

    # POST /asignacion
    protected function crearCasos(): void {
        /*$this->logger->debug("🔄 crear(): recolectando datos del POST", [
            'POST' => $_POST
        ]); */
        $id = intval($_POST['id'] ?? 0);
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
            $_SESSION['asignacion_errors'] = $errores;
            require_once __DIR__ . '/../vistas/asignacion.php';
            exit;
        }
        $this->modeloCasos->actualizarCaso($id, $nombre, $correo, $rol, $estado);
        $_SESSION['asignacion_message'] = 'Funcionario creado correctamente.';
        require_once __DIR__ . '/../vistas/registros.php';
        exit;
    }

    # PUT /asignacion
    protected function actualizarCasos(): void {
        /*$this->logger->debug("🔄 actualizarCasos(): recolectando datos del PUT", [
            'PUT' => $_POST
        ]); */
        $id     = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $rol    = trim($_POST['rol'] ?? '');
        $nuevoEstado = isset($_POST['estado']) ? 1 : 0;

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
            $this->logger->warning("⚠️ actualizarCasos(): errores de validación", ['errores' => $errores]);
            $_SESSION['asignacion_errors'] = $errores;
            header("Location: /asignacion/funcionarios/editar?id={$id}");
            exit;
        }

        $this->modeloCasos->actualizarCasos($id,  $nuevoEstado);
        $_SESSION['asignacion_message'] = 'Funcionario actualizado correctamente.';
        header('Location: /asignacion/funcionarios');
        exit;
    }

    #GET /asignacion/crearcasos
    protected function mostrarFormularioCrearCaso(): void {
        require_once __DIR__ . '/../Vistas/crearcasos.php';
    }

    protected function crearCaso(): void {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Validación básica
        if (empty($data['radicado']) || empty($data['identificacion'])) {
            echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes']);
            return;
        }
        // Calcular monto total
        $montoTotal = (float)$data['monto_original'] + (float)$data['intereses'] + (float)$data['costos'];
        // Insertar en base de datos (ejemplo)
            $resultado = $this->modeloCasos->crearCaso([
                'radicado' => $data['radicado'],
                'tipo_persona' => $data['tipo_persona'],
                'nombre' => $data['tipo_persona'] === 'natural' ? $data['nombre_apellido'] : $data['razon_social'],
                'identificacion' => $data['identificacion'],
                'tipo_tramite' => $data['tipo_tramite'],
                'estado' => $data['estado_tramite'],
                'descripcion' => $data['descripcion'],
                'monto_original' => $data['monto_original'],
                'intereses' => $data['intereses'],
                'costos' => $data['costos'],
                'monto_total' => $montoTotal,
                'fecha_creacion' => $data['fecha_creacion'],
                'fecha_asignacion' => $data['fecha_asignacion'],
                'fecha_limite_pago' => $data['fecha_limite_pago'],
                'fecha_cierre' => $data['fecha_cierre'],
                'numero_factura' => $data['numero_factura'] ?? null
            ]);
            
            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en base de datos']);
            }
            $this->logger->error("Error creando caso", [
                'data' => $data,
                'error' => $resultado
            ]);
            echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
    }

        #GET /asignacion/registros
        protected function crearRegistros(): void {
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
                $_SESSION['asignacion_errors'] = $errores;
                require_once __DIR__ . '/../vistas/asignacion.php';
                exit;
            }

            $this->modeloRegistro->insertarRegistros($nombre, $correo, $rol, $estado);
            $_SESSION['asignacion_message'] = 'Funcionario creado correctamente.';
            require_once __DIR__ . '/../vistas/registros.php';
            exit;
        }

        # GET /asignacion/registros
        protected function listarRegistros(): void {
            $this->modeloRegistro->getRegistros();
            require_once __DIR__ . '/../vistas/registros.php';
        }
    }