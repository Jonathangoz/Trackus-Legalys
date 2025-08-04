<?php
# src/Modulos/Controladores/control_adminTramites.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Modulos\Asignacion\Controladores\control_Registros;
use App\Modulos\Asignacion\Controladores\control_Asignacion;
use App\Modulos\Asignacion\Controladores\control_Crear;
use App\Comunes\seguridad\autenticacion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class control_adminTramites extends controlador_base {
    /** @var Logger */
    private Logger $logger;

    public function __construct() {
        # Inicializa Monolog para capturar todos los pasos
        $this->logger = loggers::createLogger();
    }

    /**
     * Despacha rutas para ADMIN_TRAMITE.
     *
     * @param string $uri    Ruta completa recibida
     * @param string $method "GET" o "POST"
     */
    public function handle(string $uri, string $method): void {
        # VALIDACIÓN DE SESIÓN Y ROL
        if (!autenticacion::revisarLogueoUsers()) {
            $this->logger->warning("🚫 Usuario no autenticado. Redirigiendo a /login");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        $rol = autenticacion::rolUsuario();
        if ($rol !== 'ADMIN_TRAMITE') {
            $this->logger->warning("🚫 Usuario autenticado, pero sin rol ADMIN_TRAMITE. Rol actual: {$rol}");
            autenticacion::logout();
            $this->redirect('/login');
            return;
        }

        # Normalizar la ruta
        $path = strtolower(rtrim($uri, '/'));
        $this->logger->info("📍 ADMIN_TRAMITE accediendo a: {$path} con método: {$method}");

        # RUTAS PRINCIPALES
        switch ($path) {
            case '/asignacion':
                $this->logger->info("↪️  Delegando a control_Asignacion");
                (new control_Asignacion())->handle($path, $method);
                break;
                
            case '/registros':
                $this->logger->info("↪️  Delegando a control_Registros");
                (new control_Registros())->handle($path, $method);
                break;
                
            case '/crearcasos':
                $this->logger->info("↪️  Delegando a control_Crear");
                (new control_Crear())->handle($path, $method);
                break;
                
            case '':
            case '/':
                # Si accede a la raíz, redirigir a asignacion por defecto
                $this->logger->info("↪️  Ruta raíz, redirigiendo a /asignacion");
                $this->redirect('/asignacion');
                break;
                
            default:
                # Si la ruta no coincide exactamente, verificar si es una subruta
                if (strpos($path, '/asignacion') === 0) {
                    $this->logger->info("↪️  Subruta de asignacion, delegando a control_Asignacion");
                    (new control_Asignacion())->handle($path, $method);
                } elseif (strpos($path, '/registros') === 0) {
                    $this->logger->info("↪️  Subruta de registros, delegando a control_Registros");
                    (new control_Registros())->handle($path, $method);
                } elseif (strpos($path, '/crearcasos') === 0) {
                    $this->logger->info("↪️  Subruta de crearcasos, delegando a control_Crear");
                    (new control_Crear())->handle($path, $method);
                } else {
                    $this->logger->warning("❓ Ruta no encontrada para ADMIN_TRAMITE: {$path}");
                    # En lugar de redirigir al login, redirigir a la página principal del rol
                    $this->redirect('/asignacion');
                }
                break;
        }
    }
}