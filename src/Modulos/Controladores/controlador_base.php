<?php
// src/controladores/controlador_base.php
//declare(strict_types=1);

namespace App\Modulos\Controladores;

abstract class controlador_base {

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}
