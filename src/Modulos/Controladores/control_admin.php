<?php
// src/Modulos/Controladores/control_admin.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Controladores\controlador_base;

class control_admin extends controlador_base
{
    /**
     * Método único que “maneja” todas las rutas bajo /admin.
     * Recibe $uri (p.ej. "/admin/dashboard") y $method ("GET"|"POST") y decide.
     */
    public function handle(string $uri, string $method): void {
        // Validar que siga autenticado y con rol correcto
        if (!autenticacion::revisarLogueoUsers() || autenticacion::rolUsuario() !== 'ADMIN') {
            autenticacion::logout();
            $this->redirect('/login');
        }

        switch ("$method $uri") {
            case 'GET /admin/dashboard':
                $this->dashboard();
                break;

            case 'GET /admin/usuarios':
                $this->usuarios();
                break;

            case 'GET /admin/deudores':
                $this->deudores();
                break;

            // Ejemplo: POST /admin/usuarios/crear (puedes añadir más lógicas aquí)
            // case 'POST /admin/usuarios/crear':
            //     $this->crearUsuario();
            //     break;

            default:
                http_response_code(404);
                echo "Admin: ruta no encontrada.";
                break;
        }
    }

    // Métodos concretos:

    public function dashboard(): void {
        // Carga datos, por ejemplo:
        // $data = ['usuarioNombre' => autenticacion::nombresUsuario()];
        // renderView('admin/dashboard', $data);
        $this->redirect('/admin/dashboard'); // o incluir la vista directamente
    }

    public function usuarios(): void {
        // $usuarios = Usuario::all();
        // renderView('admin/usuarios', ['usuarios'=>$usuarios]);
        $this->redirect('/admin/usuarios');
    }

    public function deudores(): void {
        // $deudores = Deudor::allActivos();
        // renderView('admin/deudores', ['deudores'=>$deudores]);
        $this->redirect('/admin/deudores');
    }

    // Puedes agregar más métodos: crearUsuario(), editarUsuario(), etc.
}
