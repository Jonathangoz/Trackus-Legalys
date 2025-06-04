<?php
// src/Modulos/Controladores/control_usuarios.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Controladores\controlador_base;

class control_usuarios extends controlador_base
{
    public function handle(string $uri, string $method): void {
        if (!autenticacion::revisarLogueoUsers() || autenticacion::rolUsuario() !== 'USUARIO') {
            autenticacion::logout();
            $this->redirect('/login');
        }

        switch ("$method $uri") {
            case 'GET /usuario/consultas':
                $this->consultas();
                break;
            // Otras rutas USUARIO...
            default:
                http_response_code(404);
                echo "Usuario: ruta no encontrada.";
                break;
        }
    }

    public function consultas(): void {
        // $data = [ 'consultas' => Consulta::porUsuario($_SESSION['user_id']) ];
        $this->redirect('/usuario/consultas');
    }
}
