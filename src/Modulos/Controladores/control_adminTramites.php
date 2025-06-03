<?php
// src/modulos/controladores/control_adminTramites.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;

class control_adminTramites extends controlador_base
{
    /**
     * GET /admin_tramite/dashboard
     */
    public function dashboard(): void
    {
        if (! autenticacion::checkUserIsLogged() || autenticacion::getUserRole() !== 'ADMIN_TRAMITE') {
            autenticacion::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => autenticacion::getUserFullName(),
            // … otros datos para trámite …
        ];
        $this->renderView('admin_tramite/dashboard', $data);
    }

    // Otros métodos específicos de ADMIN_TRAMITE…
}
