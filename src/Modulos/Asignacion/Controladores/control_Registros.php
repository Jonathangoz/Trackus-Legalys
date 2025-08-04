<?php
// src/Modulos/Asignacion/Controladores/control_Registros.php (controlador del modelo y vistas)
declare(strict_types=1);

namespace App\Modulos\Asignacion\Controladores;

use App\Modulos\Controladores\controlador_base;
use App\Comunes\seguridad\autenticacion;
use App\Modulos\Asignacion\Modelos\registros;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_Registros extends controlador_base {
    protected registros $modeloRegistro;
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializar modelo y logger
        $this->modeloRegistro = new registros();
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que inicien en /registros
     * @param string $uri
     * @param string $method
     */
    public function handle(string $uri, string $method): void {
        # Para evitar distinciones de mayúsculas/minúsculas:
        $path = strtolower($uri);
        $this->logger->info("🏷️  control_Registros::handle() invocado para: {$method} {$path}");

        # Verificar autenticación y rol ADMIN_TRAMITE (redundante pero seguro)
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado en Registros, redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario sin rol ADMIN_TRAMITE en Registros. Rol actual: {$rol}, redirigiendo a /login");
            $this->redirect('/login');
            return;
        }

        #Redirige al modelo segun metodo, uri y lo redirecciona a la funcion principal del modelo, recorriendo lo necesario.
        $rutas = "{$method} " . strtolower(rtrim($uri, '/'));
        switch ($rutas) {

        case 'GET /registros':
        case 'POST /registros':
            $this->listarRegistros();
            $this->logger->info("📝 Mostrando registros de casos");
            return;

        default:
            $this->logger->warning("❓ registros::handle(): ruta no encontrada ({$uri})");
            http_response_code(404);
            echo "Ruta no encontrada: {$uri}";
            return;
        }
    }

    # DIRIGE AL MODELO Y CONSULTAS ESPECIFICAS

    # GET /registros
    protected function listarRegistros(): void {
        $datosRegis = [
            'registros' => $this->modeloRegistro->getRegistros(),
        ];
        extract($datosRegis);
        require_once __DIR__ . '/../Vistas/Registros.php';
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