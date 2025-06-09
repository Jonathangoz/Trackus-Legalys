<?php
# src/Modulos/Controladores/control_deudores.php (controlador deudores o usuarios obligados al pago)
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Consultas\Controladores\control_obligados;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_deudores extends controlador_base {
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializa Monolog para capturar todos los pasos
        $this->logger = loggers::createLogger();
        $this->logger->info("💼 control_abogados::__construct() inicializado");
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
        $this->logger->info("🏷️  control_abogados::handle() invocado para: {$method} {$path}");

        # VALIDACIÓN DE SESIÓN Y ROL
        if (! autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        $this->logger->debug("👤 Rol obtenido en sesión: {$rol}");
        if ($rol !== 'DEUDOR') {
            $this->logger->warning("🚫 Usuario autenticado, pero sin rol ABOGADO. Cierre de sesión.");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # RUTAS DE ABOGADO
        # Aquí puedes agregar rutas específicas bajo "/ABOGADO"
        # (por ejemplo: "/ABOGADO/usuarios", "/ABOGADO/deudores", etc.)
        # En este ejemplo mínimo, si llaman exactamente a "/ABOGADO", redirigimos a "/dashboard".
        if ($path === '/DEUDOR' || $path === '/DEUDOR/') {
            $this->logger->info("↪️  GET /ABOGADO → redirigiendo a /index");
            $this->redirect('/consultas');
            return;
        }

        # RUTAS DE DASHBOARD
        # Si la URI comienza con "/dashboard", delegamos al módulo Dashboard
        if (strpos($path, '/consultas') === 0) {
            $this->logger->info("↪️  Delegando al módulo Consultas: {$method} {$path}");
            $dashboardCtrl = new control_obligados();
            $dashboardCtrl->handle($path, $method);
            $this->logger->info("✔️  control_obligados->handle() completado para: {$method} {$path}");
            return;
        }

        # OTRAS RUTAS DE MÓDULOS (ejemplo CobroCoactivo)
        # Si en el futuro agregas, por ejemplo, un módulo "/cobrocoactivo", bastaría con:
        if (strpos($path, '/cobrocoactivo') === 0) {
            $this->logger->info("↪️  Delegando al módulo CobroCoactivo: {$method} {$path}");
            $cobroCtrl = new control_deudores();
            $cobroCtrl->handle($path, $method);
            $this->logger->info("✔️  control_Coactivo->handle() completado para: {$method} {$path}");
            return;
        }

        # RUTA NO ENCONTRADA
        $this->logger->warning("❓ control_ABOGADO::handle(): ruta no encontrada ({$path})");
        http_response_code(404);
        echo "ABOGADO: ruta no encontrada ({$path})";
    }

    # Si en algún momento necesitas métodos concretos para "/ABOGADO/xxx",
    # puedes agregarlos aquí. Por ejemplo:
    # protected function listarUsuarios(): void { ... }
}