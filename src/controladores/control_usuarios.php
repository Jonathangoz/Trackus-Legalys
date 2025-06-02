<?php
// src/controladores/control_usuarios.php
declare(strict_types=1);

namespace App\controladores;

use App\seguridad\autenticacion;

class control_usuarios extends controlador_base
{
    /**
     * GET /usuario/consultas
     */
    public function consultas(): void
    {
        if (! autenticacion::checkUserIsLogged() || autenticacion::getUserRole() !== 'USUARIO') {
            autenticacion::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => autenticacion::getUserFullName(),
            // 'misConsultas' => Consulta::allForUser($_SESSION['user_id'])
        ];
        $this->renderView('usuario/consultas', $data);
    }

    // Puedes añadir más acciones de usuario si en el futuro se requiere.
}
