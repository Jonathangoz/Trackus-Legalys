<?php
// src/Modulos/Dashboard/routes.php

function verificarAcceso(array $rutaConfig, string $rolUsuario): bool {
    if (!isset($rutaConfig['roles_permitidos']) || ! is_array($rutaConfig['roles_permitidos'])) {
        // Si no está definido, por seguridad niega el acceso
        return false;
    }
    return in_array($rolUsuario, $rutaConfig['roles_permitidos'], true);
}

return [
    '/dashboard/funcionarios' => [
        'controller' => 'App\\Modulos\\Dashboard\\Controladores\\control_Dashboard',
        'method'           => 'listarFunc',
        'verbs'            => 'GET',
        'roles_permitidos' => ['ADMIN'],
    ],
    // …
];

/*return [
    // URI                             => [ Controlador,         método,             HTTP,    rolesPermitidos ]
    '/Dashboard'                       => ['control_Dashboard', 'Dashboard',         'GET, POST',    ['ADMIN']],
    '/dashboard/funcionarios'          => ['control_Dashboard', 'listarFunc',        'GET',    ['ADMIN']],
    '/dashboard/funcionarios/crear'    => ['control_Dashboard', 'crearForm',         'GET',    ['ADMIN']],
    '/dashboard/funcionarios/editar'   => ['control_Dashboard', 'editarForm',        'GET',    ['ADMIN']],
    '/dashboard/funcionarios/eliminar' => ['control_Dashboard', 'eliminar',          'POST',   ['ADMIN']],
    '/dashboard/funcionarios/activar'  => ['control_Dashboard', 'activar',           'POST',   ['ADMIN']],
    '/dashboard/auditoria'             => ['control_Dashboard', 'verAuditoria',      'GET',    ['ADMIN']],
    '/dashboard/estadisticas'          => ['control_Dashboard', 'estadisticas',      'GET',    ['ADMIN']],
    // … en el futuro puedes añadir más rutas de Dashboard (reportes globales, calendario, notificaciones, etc.) …
];

