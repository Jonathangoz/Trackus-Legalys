<?php
// src/controladores/control_admin.php
declare(strict_types=1);

namespace App\controladores;

use App\controladores\controlador_base;
use App\seguridad\autenticacion;

class control_admin extends controlador_base
{
    /**
     * GET /admin/dashboard
     */
    public function dashboard(): void
    {
        // 1) Verificar que esté autenticado
        if (! autenticacion::checkUserIsLogged()) {
            $this->redirect('/login');
        }
        // 2) Verificar rol = 'ADMIN'
        if (autenticacion::getUserRole() !== 'ADMIN') {
            autenticacion::logout();
            $this->redirect('/login');
        }

        // 3) Cargar datos necesarios (ejemplo: contar usuarios, deudores, etc.)
        $data = [
            'usuarioNombre' => autenticacion::getUserFullName(),
            // 'cantidadUsuarios' => Usuario::countAll(),
            // 'cantidadDeudores' => Deudor::countAllActivos(),
        ];
        // 4) Renderizar la vista desde src/Views/admin/dashboard.php
        $this->renderView('admin/dashboard', $data);
    }

    /**
     * GET /admin/usuarios
     * Muestra CRUD de usuarios (solo ADMIN)
     */
    public function usuarios(): void
    {
        if (! autenticacion::checkUserIsLogged() || autenticacion::getUserRole() !== 'ADMIN') {
            autenticacion::logout();
            $this->redirect('/login');
        }
        // Cargar lista de usuarios activos/inactivos
        // $usuarios = Usuario::all();
        $this->renderView('admin/usuarios' /*, ['usuarios' => $usuarios] */);
    }

    /**
     * GET /admin/deudores
     */
    public function deudores(): void
    {
        if (! autenticacion::checkUserIsLogged() || autenticacion::getUserRole() !== 'ADMIN') {
            autenticacion::logout();
            $this->redirect('/login');
        }
        // $deudores = Deudor::allActivos();
        $this->renderView('admin/deudores' /*, ['deudores' => $deudores] */);
    }

    // Agrega más métodos según necesidades: createUsuario(), editUsuario(), deleteUsuario(), etc.
}
