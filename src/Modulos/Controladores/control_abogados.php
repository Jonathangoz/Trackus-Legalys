<?php
# src/Modulos/Controladores/control_abogados.php  (controlador principal del rol para redireccionar al modulo correspondiente)
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Modulos\Deudores\Controladores\control_Coactivo;
use App\Modulos\Deudores\Controladores\control_Deudores;
use App\Comunes\seguridad\autenticacion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_abogados extends controlador_base {
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializa Monolog para capturar todos los pasos
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas que empiecen en /ABOGADO
     *
     * @param string $uri    Ruta completa recibida
     * @param string $method "GET" o "POST"
     */
    public function handle(string $uri, string $method): void {

        # 1) VALIDACIÓN DE SESIÓN Y ROL
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ABOGADO') {
            $this->logger->warning("🚫 Usuario autenticado, pero sin rol ABOGADO. Rol actual: {$rol}");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # Normalizar la ruta
        $path = strtolower(rtrim($uri, '/'));
        $this->logger->info("📍 ABOGADO accediendo a: {$path} con método: {$method}");

        # RUTAS PRINCIPALES
        switch ($path) {
            case '/cobrocoactivo':
                $this->logger->info("↪️  Delegando a control_Coactivo");
                (new control_Coactivo())->handle($path, $method);
                break;
                
            case '/deudores':
                $this->logger->info("↪️  Delegando a control_Deudores");
                (new control_Deudores())->handle($path, $method);
                break;
                
            case '':
            case '/':
                # Si accede a la raíz, redirigir a cobrocoactivo por defecto
                $this->logger->info("↪️  Ruta raíz, redirigiendo a /cobrocoactivo");
                $this->redirect('/cobrocoactivo');
                break;
                
            default:
                # Si la ruta no coincide exactamente, verificar si es una subruta
                if (strpos($path, '/cobrocoactivo') === 0) {
                    $this->logger->info("↪️  Subruta de cobrocoactivo, delegando a control_Coactivo");
                    (new control_Coactivo())->handle($path, $method);
                } elseif (strpos($path, '/deudores/obligados') === 0) {
                    $this->logger->info("↪️  Subruta de deudores, delegando a control_Deudores");
                    (new control_Deudores())->handle($path, $method);
                } elseif (strpos($path, '/cobrocoactivo/formularios') === 0) {
                    $this->logger->info("↪️  Subruta de cobrocoactivo/formularios, delegando a control_Coactivo");
                    (new control_Coactivo())->handle($path, $method); 
                }else {
                    $this->logger->warning("❓ Ruta no encontrada para ADMIN_TRAMITE: {$path}");
                    # En lugar de redirigir al login, redirigir a la página principal del rol
                    $this->redirect('/cobrocoactivo');
                }
                break;

        }

    }

}