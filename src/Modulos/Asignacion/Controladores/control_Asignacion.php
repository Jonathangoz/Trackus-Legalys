<?php
// src/Modulos/Asignacion/Controladores/control_Asignacion.php (controlador del modelo y vistas)
declare(strict_types=1);

namespace App\Modulos\Asignacion\Controladores;

use App\Modulos\Controladores\controlador_base;
use App\Modulos\Asignacion\Modelos\asignacion;
use App\Comunes\seguridad\autenticacion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Asignacion extends controlador_base {
    protected asignacion $modeloAsignacion;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializar modelo y logger
        $this->modeloAsignacion = new asignacion();
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

        # Revisar si esta logueado y tiene el rol correcto
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado en Asignacion, redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }
        
        # CORRECCIÓN: Usar autenticacion::rolUsuario() en lugar de $_SESSION directamente
        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN_TRAMITE en Asignacion. Rol actual: {$rol}, redirigiendo a /login");
            $this->redirect('/login');
            return;
        }

        #Redirige al modelo segun metodo, uri y lo redirecciona a la funcion principal del modelo, recorriendo lo necesario.
        $rutas = "{$method} " . strtolower(rtrim($uri, '/'));
        switch ($rutas) {

        case 'GET /asignacion':
        case 'POST /asignacion':
            $this->listadoAsignacion();
            $this->logger->info("📝 Mostrando listado de asignación");
            return;

        default:
            $this->logger->warning("❓ asignacion::handle(): ruta no encontrada ({$uri})");
            http_response_code(404);
            echo "Ruta no encontrada: {$uri}";
            return;
        }
    }

    # DIRIGE AL MODELO Y CONSULTAS ESPECIFICAS

    # GET /asignacion
    protected function listadoAsignacion(): void {
        $datosAsig = [
            'abogados' => $this->modeloAsignacion->getAbogados(),
        ];
        extract($datosAsig);
        require_once __DIR__ . '/../Vistas/asignacion.php';
    }
}