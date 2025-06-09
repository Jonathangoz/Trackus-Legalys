<?php
# src/controladores/controlador_base.php (ayuda a redireccionar de forma global segun vabriable asiganda a recorrer las rutas)
declare(strict_types=1);

namespace App\Modulos\Controladores;

abstract class controlador_base {

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}