<?php
// src/Controllers/UsuarioController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;

class UsuarioController extends BaseController
{
    /**
     * GET /usuario/consultas
     */
    public function consultas(): void
    {
        if (! AuthService::checkUserIsLogged() || AuthService::getUserRole() !== 'USUARIO') {
            AuthService::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => AuthService::getUserFullName(),
            // 'misConsultas' => Consulta::allForUser($_SESSION['user_id'])
        ];
        $this->renderView('usuario/consultas', $data);
    }

    // Puedes añadir más acciones de usuario si en el futuro se requiere.
}
