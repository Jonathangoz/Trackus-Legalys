<?php
// src/Controllers/AdminController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;

class AdminController extends BaseController
{
    /**
     * GET /admin/dashboard
     */
    public function dashboard(): void
    {
        // 1) Verificar que esté autenticado
        if (! AuthService::checkUserIsLogged()) {
            $this->redirect('/login');
        }
        // 2) Verificar rol = 'ADMIN'
        if (AuthService::getUserRole() !== 'ADMIN') {
            AuthService::logout();
            $this->redirect('/login');
        }

        // 3) Cargar datos necesarios (ejemplo: contar usuarios, deudores, etc.)
        $data = [
            'usuarioNombre' => AuthService::getUserFullName(),
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
        if (! AuthService::checkUserIsLogged() || AuthService::getUserRole() !== 'ADMIN') {
            AuthService::logout();
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
        if (! AuthService::checkUserIsLogged() || AuthService::getUserRole() !== 'ADMIN') {
            AuthService::logout();
            $this->redirect('/login');
        }
        // $deudores = Deudor::allActivos();
        $this->renderView('admin/deudores' /*, ['deudores' => $deudores] */);
    }

    // Agrega más métodos según necesidades: createUsuario(), editUsuario(), deleteUsuario(), etc.
}
