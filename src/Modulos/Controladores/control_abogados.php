<?php
# src/Modulos/Controladores/control_abogados.php  (controlador principal del rol para redireccionar al modulo correspondiente)
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\CobroCoactivo\Controladores\control_Coactivo;
use App\Comunes\utilidades\loggers;
use App\Modulos\Deudores\Controladores\control_Deudores;
use Monolog\Logger;

class control_abogados extends controlador_base {
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializa Monolog para capturar todos los pasos
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que empiecen en /ABOGADO o /Deudores.php.
     *
     * @param string $uri    Ruta completa recibida (p.ej. "/dashboard/funcionarios")
     * @param string $method "GET" o "POST"
     */
    public function handle(string $uri, string $method): void {
        # Para evitar distinciones de mayúsculas/minúsculas:
        $path = strtolower($uri);

        # 1) VALIDACIÓN DE SESIÓN Y ROL
        if (! autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ABOGADO') {
            $this->logger->warning("🚫 Usuario autenticado, pero sin rol ABOGADO. Cierre de sesión.");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # RUTAS DE ABOGADO
        # Aquí puedes agregar rutas específicas bajo "/ABOGADO"
        # (por ejemplo: "/ABOGADO/usuarios", "/ABOGADO/deudores", etc.)
        # En este ejemplo mínimo, si llaman exactamente a "/ABOGADO", redirigimos a "/dashboard".
        if ($path === '/ABOGADO' || $path === '/ABOGADO/') {
            $this->logger->info("↪️  GET /ABOGADO → redirigiendo a /index");
            $this->redirect('/deudores');
            return;
        }

        # 3) RUTAS DE DASHBOARD
        # Si la URI comienza con "/dashboard", delegamos al módulo Dashboard
        if (strpos($path, '/deudores') === 0) {
            $dashboardCtrl = new control_Deudores();
            $dashboardCtrl->handle($path, $method);
            return;
        }

        # 4) OTRAS RUTAS DE MÓDULOS (ejemplo CobroCoactivo)
        # Si en el futuro agregas, por ejemplo, un módulo "/cobrocoactivo", bastaría con:
        if (strpos($path, '/cobrocoactivo') === 0) {
            $cobroCtrl = new control_Coactivo();
            $cobroCtrl->handle($path, $method);
            return;
        }

        # 5) RUTA NO ENCONTRADA
        $this->logger->warning("❓ control_ABOGADO::handle(): ruta no encontrada ({$path})");
        http_response_code(404);
        echo "ABOGADO: ruta no encontrada ({$path})";
    }

}