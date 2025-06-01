<?php
// src/Controllers/AbogadoController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;

class AbogadoController extends BaseController
{
    /**
     * GET /abogado/procesos
     */
    public function procesos(): void
    {
        if (! AuthService::checkUserIsLogged() || ! in_array(AuthService::getUserRole(), ['ABOGADO_1','ABOGADO_2','ABOGADO_3'])) {
            AuthService::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => AuthService::getUserFullName(),
            // 'procesos' => Proceso::allByAbogado($_SESSION['user_id'])
        ];
        $this->renderView('abogado/procesos', $data);
    }

    /**
     * GET /abogado/calendario
     */
    public function calendario(): void
    {
        if (! AuthService::checkUserIsLogged() || ! in_array(AuthService::getUserRole(), ['ABOGADO_1','ABOGADO_2','ABOGADO_3'])) {
            AuthService::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => AuthService::getUserFullName(),
            // 'eventos' => Evento::allForAbogado($_SESSION['user_id'])
        ];
        $this->renderView('abogado/calendario', $data);
    }

    // Otros métodos para ABAOGADO (documentación, informes, etc.)
}
