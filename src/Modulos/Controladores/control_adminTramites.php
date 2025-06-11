<?php
# src/Modulos/Controladores/control_adminTramites.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\CobroCoactivo\Controladores\control_Coactivo;
use App\Modulos\asignacion\Controladores\control_asignacion;
use App\Comunes\utilidades\loggers;
use App\Modulos\Asignacion\Modelos\asignacion;
use Monolog\Logger;

class control_adminTramites extends controlador_base {
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializa Monolog para capturar todos los pasos
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que empiecen en /admin_tramite.
     *
     * @param string $uri    Ruta completa recibida /asigancion
     * @param string $method "GET" o "POST"
     */
    public function handle(string $uri, string $method): void {
        # Para evitar distinciones de mayúsculas/minúsculas:
        $path = strtolower($uri);

        # VALIDACIÓN DE SESIÓN Y ROL
        if (! autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        $this->logger->debug("👤 Rol obtenido en sesión: {$rol}");
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario autenticado, pero sin rol ADMIN. Cierre de sesión.");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # RUTAS DE ADMIN_TRAMITE PRINCIPAL
        # Ruta específica para "/adminTramite"
        if ($path === '/ADMIN_TRAMITE' || $path === '/ADMIN_TRAMITE/') {
            $this->redirect('/asignacion');
            return;
        }
        
        # RUTAS DE ASIGNACIÓN MOdulo principal (Modulo Asigancion)
        # Si la URI comienza con "asignacion", se dirije al módulo Asigancion
        if (strpos($path, '/asignacion') === 0) {
            $asigCtrl = new control_Asignacion();
            $asigCtrl->handle($path, $method);
            return;
        }

        # OTRAS RUTAS DE MÓDULOS - CobroCoactivo
        # módulo "/cobrocoactivo":
        if (strpos($path, '/cobrocoactivo') === 0) {
            $cobroCtrl = new control_Coactivo();
            $cobroCtrl->handle($path, $method);
            return;
        } 

        # RUTA NO ENCONTRADA
        $this->logger->warning("❓ control_adminTramites::handle(): ruta no encontrada ({$path})");
        http_response_code(404);
        echo "Admin_Tramite: ruta no encontrada ({$path})";
    }
}