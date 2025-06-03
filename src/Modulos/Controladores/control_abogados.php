<?php
// src/Modulos/controladores/control_abogados.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Controladores\controlador_base;

class control_abogados extends controlador_base
{
    /**
     * GET /abogado/procesos
     */
    public function procesos(): void
    {
        if (! autenticacion::checkUserIsLogged() || ! in_array(autenticacion::getUserRole(), ['ABOGADO_1','ABOGADO_2','ABOGADO_3'])) {
            autenticacion::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => autenticacion::getUserFullName(),
            // 'procesos' => Proceso::allByAbogado($_SESSION['user_id'])
        ];
        $this->renderView('abogado/procesos', $data);
    }

    /**
     * GET /abogado/calendario
     */
    public function calendario(): void
    {
        if (! autenticacion::checkUserIsLogged() || ! in_array(autenticacion::getUserRole(), ['ABOGADO_1','ABOGADO_2','ABOGADO_3'])) {
            autenticacion::logout();
            $this->redirect('/login');
        }
        $data = [
            'usuarioNombre' => autenticacion::getUserFullName(),
            // 'eventos' => Evento::allForAbogado($_SESSION['user_id'])
        ];
        $this->renderView('abogado/calendario', $data);
    }

    // Otros métodos para ABAOGADO (documentación, informes, etc.)
}
