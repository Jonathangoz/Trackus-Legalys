<?php
# src/Modulos/Controladores/control_adminTramites.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Comunes\utilidades\loggers;
use App\Modulos\Asignacion\Controladores\control_Crear;
use App\Modulos\Asignacion\Controladores\control_Registros;
use App\Modulos\Asignacion\Controladores\control_Asignacion;
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
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario autenticado, pero sin rol ADMIN. Cierre de sesión.");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $path = strtolower(rtrim($uri, '/'));

        # RUTAS DE DASHBOARD
        # Si la URI comienza con "/dashboard", delegamos al módulo Dashboard
        if (strpos($path, '/asignacion') === 0) {
            (new control_Asignacion())->handle($path, $method);
            return;
        } elseif (strpos($path, '/registros') === 0) {
            (new control_Registros())->handle($path, $method);
            return;
        } elseif (strpos($path, '/crearcasos') === 0) {
            (new control_Crear())->handle($path, $method);
            return;
        }

        # 3) Si no matchea ninguna, 404
        $this->logger->warning("❓ control_adminTramites::handle(): ruta no encontrada ({$uri})");
        $this->redirect('/login');
    }
}