<?php
// src/controladores/controlador_base.php
//declare(strict_types=1);

namespace App\Modulos\Controladores;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

abstract class controlador_base {

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}
