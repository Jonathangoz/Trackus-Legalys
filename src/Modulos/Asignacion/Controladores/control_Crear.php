<?php
// src/Modulos/Asignacion/Controladores/control_Asignacion.php (controlador del modelo y vistas)
declare(strict_types=1);

namespace App\Modulos\Asignacion\Controladores;

use App\Modulos\Asignacion\Modelos\casos;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Crear {
    protected casos $modeloCasos;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializar modelo y logger
        $this->modeloCasos = new casos();
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
        $this->logger->info("🏷️  control_Creaarcasos::handle() invocado para: {$method} {$path}");

        # Verificar autenticación y rol ADMIN_TRAMITE (redundante pero seguro)
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            $this->logger->warning("🚫 Usuario no autenticado en Asignacion, redirigiendo a /login");
            header('Location: /login');
            exit;
        }
        $rol = $_SESSION['tipo_rol'] ?? null;
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN_TRAMITE en el Index, redirigiendo a /login");
            header('Location: /login');
            exit;
        }

        #Redirige al modelo segun metodo, uri y lo redirecciona a la funcion principal del modelo, recorriendo lo necesario.
        $rutas = "{$method} " . strtolower(rtrim($uri, '/'));
        switch ($rutas) {

        case 'GET /crearcasos':
        case 'POST /crearcasos':
            $this->mostrarFormularioCrearCaso();
            $this->logger->info("📝 Mostrando formulario para crear caso");
            return;

        default:
            $this->logger->warning("❓ asignacion::handle(): ruta no encontrada ({$uri})");
            http_response_code(404);
            echo "Ruta no encontrada: {$uri}";
            return;
        }
    }

    # DIRIGE AL MODELO Y CONSULTAS ESPECIFICAS

    #GET /crearcasos
    protected function mostrarFormularioCrearCaso(): void {
        $datosCaso = [
            'abogados' => $this->modeloCasos->getCaso(),
        ];
        extract($datosCaso);
        require_once __DIR__ . '/../Vistas/crearcasos.php';
    }

   /* protected function crearCaso(): void {
    // Ejemplo simplificado
    $data = $_POST;
    $this->modeloCasos->insertar([
      'radicado' => $data['radicado'],
      'deudor_id' => $data['deudor_id'],
      // … demás columnas …
    ]);
    // Auditoría, validaciones, etc.
    } */

}