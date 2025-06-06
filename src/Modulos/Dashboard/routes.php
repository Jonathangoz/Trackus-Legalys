<?php
// src/Modulos/Dashboard/routes.php

function verificarAcceso($rutaConfig, $rolUsuario) {
    return in_array($rolUsuario, $rutaConfig[3]); // rolesPermitidos
}

return [
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

