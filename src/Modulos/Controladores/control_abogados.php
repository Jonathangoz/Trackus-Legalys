<?php
// src/Modulos/Controladores/control_abogados.php
declare(strict_types=1);

namespace App\Modulos\Controladores;

use App\Comunes\seguridad\autenticacion;
use App\Modulos\Controladores\controlador_base;

class control_abogados extends controlador_base
{
    public function handle(string $uri, string $method): void {
        if (!autenticacion::revisarLogueoUsers() 
            || !in_array(autenticacion::rolUsuario(), ['ABOGADO_1','ABOGADO_2','ABOGADO_3'])) {
            autenticacion::logout();
            $this->redirect('/login');
        }

        switch ("$method $uri") {
            case 'GET /abogado/procesos':
                $this->procesos();
                break;
            case 'GET /abogado/calendario':
                $this->calendario();
                break;
            // Otras rutas ABOGADO...
            default:
                http_response_code(404);
                echo "Abogado: ruta no encontrada.";
                break;
        }
    }

    public function procesos(): void {
        // $data = [ 'procesos' => Proceso::porAbogado($_SESSION['user_id']) ];
        $this->redirect('/abogado/procesos');
    }

    public function calendario(): void {
        // $data = [ 'eventos' => Evento::porAbogado($_SESSION['user_id']) ];
        $this->redirect('/abogado/calendario');
    }
}
