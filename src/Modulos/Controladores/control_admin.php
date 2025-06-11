<?php
# src/Modulos/Controladores/control_admin.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Dashboard\Controladores\control_Dashboard;
use App\Modulos\CobroCoactivo\Controladores\control_Coactivo;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_admin extends controlador_base {
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializa Monolog para capturar todos los pasos
        $this->logger = loggers::createLogger();
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

        # VALIDACIÓN DE SESIÓN Y ROL
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ADMIN') {
            $this->logger->warning("🚫 Usuario autenticado, pero sin rol ADMIN. Cierre de sesión.");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # RUTAS DE ADMIN
        # Aquí puedes agregar rutas específicas bajo "/admin"
        # (por ejemplo: "/admin/usuarios", "/admin/deudores", etc.)
        # En este ejemplo mínimo, si llaman exactamente a "/admin", redirigimos a "/dashboard".
        if ($path === '/ADMIN' || $path === '/ADMIN/') {
            $this->redirect('/dashboard');
            return;
        }

        # RUTAS DE DASHBOARD
        # Si la URI comienza con "/dashboard", delegamos al módulo Dashboard
        if (strpos($path, '/dashboard') === 0) {
            $dashboardCtrl = new control_Dashboard();
            $dashboardCtrl->handle($path, $method);
            return;
        }

        # OTRAS RUTAS DE MÓDULOS (ejemplo CobroCoactivo)
        # Si en el futuro agregas, por ejemplo, un módulo "/cobrocoactivo", bastaría con:
        if (strpos($path, '/cobrocoactivo') === 0) {
            $cobroCtrl = new control_Coactivo();
            $cobroCtrl->handle($path, $method);
            return;
        }

        # RUTA NO ENCONTRADA
        $this->logger->warning("❓ control_admin::handle(): ruta no encontrada ({$path})");
        http_response_code(404);
        echo "Admin: ruta no encontrada ({$path})";
        $this->redirect('/login');
    }

}