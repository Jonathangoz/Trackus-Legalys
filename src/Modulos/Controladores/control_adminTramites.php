<?php
// src/Modulos/Controladores/control_adminTramites.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Controladores\controlador_base;

class control_adminTramites extends controlador_base
{
    public function handle(string $uri, string $method): void {
        if (!autenticacion::revisarLogueoUsers() || autenticacion::rolUsuario() !== 'ADMIN_TRAMITE') {
            autenticacion::logout();
            $this->redirect('/login');
        }

        switch ("$method $uri") {
            case 'GET /admin_tramite/dashboard':
                $this->dashboard();
                break;
            // Agrega aquí otras rutas de ADMIN_TRAMITE, p.ej. POST /admin_tramite/expedientes/agregar, etc.
            default:
                http_response_code(404);
                echo "AdminTramite: ruta no encontrada.";
                break;
        }
    }

    public function dashboard(): void {
        // $data = [...];
        $this->redirect('/admin_tramite/dashboard');
    }

    // Otros métodos de ADMIN_TRAMITE…
}
