<?php
// src/Controllers/BaseController.php
declare(strict_types=1);

namespace App\controladores;

abstract class controlador_base
{
    /**
     * Renderiza la vista en src/Views/<$ruta>.php con las variables dadas.
     */
    protected function renderView(string $ruta, array $variables = []): void
    {
        extract($variables, EXTR_OVERWRITE);
        ob_start();
        include __DIR__ . "/../vistas/{$ruta}.php";
        $contenido = ob_get_clean();
        echo $contenido;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
