<?php
# src/Modulos/Controladores/control_adminTramites.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Asignacion\Controladores\control_Asignacion;
use App\Modulos\CobroCoactivo\Controladores\control_Coactivo;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_adminTramites extends controlador_base {
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializa Monolog para capturar todos los pasos
        $this->logger = loggers::createLogger();
        $this->logger->info("💼 control_adminTramitesTramites::__construct() inicializado");
    }

    /**
     * Despacha rutas que empiecen en /admin o /dashboard.
     *
     * @param string $uri    Ruta completa recibida (p.ej. "/dashboard/funcionarios")
     * @param string $method "GET" o "POST"
     */
    public function handle(string $uri, string $method): void {
        # Para evitar distinciones de mayúsculas/minúsculas:
        $path = strtolower($uri);
        $this->logger->info("🏷️  control_adminTramites::handle() invocado para: {$method} {$path}");

        # 1) VALIDACIÓN DE SESIÓN Y ROL
        # ------------------------------------------------
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

        # 2) RUTAS DE ADMIN_TRAMITE
        # ------------------------------------------------
        # Aquí puedes agregar rutas específicas bajo "/admin"
        # (por ejemplo: "/admin/usuarios", "/admin/deudores", etc.)
        # En este ejemplo mínimo, si llaman exactamente a "/admin_tramites", redirigimos a "/cobrocoactivo".
        if ($path === '/ADMIN_TRAMITE' || $path === '/ADMIN_TRAMITE/') {
            $this->logger->info("↪️  GET /admin_tramite → redirigiendo a /cobrocoactivo");
            $this->redirect('/cobrocoactivo');
            return;
        }

        # 3) RUTAS DE DASHBOARD
        # ------------------------------------------------
        # Si la URI comienza con "cobrocoactivo", delegamos al módulo Dashboard
        if (strpos($path, '/cobrocoactivo') === 0) {
            $this->logger->info("↪️  Delegando al módulo Dashboard: {$method} {$path}");
            $cobroCtrl = new control_Coactivo();
            $cobroCtrl->handle($path, $method);
            $this->logger->info("✔️  control_Coactivo->handle() completado para: {$method} {$path}");
            return;
        }

        # 4) OTRAS RUTAS DE MÓDULOS (ejemplo CobroCoactivo)
        # ------------------------------------------------
        # Si en el futuro agregas, por ejemplo, un módulo "/cobrocoactivo", bastaría con:
    /*    if (strpos($path, '/asignacion') === 0) {
            $this->logger->info("↪️  Delegando al módulo CobroCoactivo: {$method} {$path}");
            $AsigCtrl = new control_Asignacion();
            $AsigCtrl->handle($path, $method);
            $this->logger->info("✔️  control_Asignacion->handle() completado para: {$method} {$path}");
            return;
        } */

        # 5) RUTA NO ENCONTRADA
        # ------------------------------------------------
        $this->logger->warning("❓ control_adminTramites::handle(): ruta no encontrada ({$path})");
        http_response_code(404);
        echo "Admin_Tramite: ruta no encontrada ({$path})";
    }

    # Si en algún momento necesitas métodos concretos para "/admin/xxx",
    # puedes agregarlos aquí. Por ejemplo:
    # protected function listarUsuarios(): void { ... }
}