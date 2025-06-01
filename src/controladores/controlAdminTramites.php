<?php
// src/Controllers/AdminTramiteController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;

class AdminTramiteController extends BaseController
{
    /**
     * GET /admin_tramite/dashboard
     */
    public function dashboard(): void
    {
        if (! AuthService::checkUserIsLogged() || AuthService::getUserRole() !== 'ADMIN_TRAMITE') {
            AuthService::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => AuthService::getUserFullName(),
            // … otros datos para trámite …
        ];
        $this->renderView('admin_tramite/dashboard', $data);
    }

    // Otros métodos específicos de ADMIN_TRAMITE…
}
