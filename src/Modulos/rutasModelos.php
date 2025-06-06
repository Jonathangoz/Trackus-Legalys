<?php

function cargarRutasModulo(string $modulo, string $rolUsuarioActual) {
    $rutaArchivo = __DIR__ . "/../Modulos/{$modulo}/routes.php";
    if (!file_exists($rutaArchivo)) return [];

    $rutas = include $rutaArchivo;
    $rutasPermitidas = [];

    foreach ($rutas as $uri => [$controlador, $metodo, $http, $roles]) {
        if (in_array($rolUsuarioActual, $roles)) {
            $rutasPermitidas[$uri] = [$controlador, $metodo, $http];
        }
    }

    return $rutasPermitidas;
}
