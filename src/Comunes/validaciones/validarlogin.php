<?php
// src/Comunes/validaciones/validarlogin.php
#declare(strict_types=1);

namespace App\Comunes\validaciones;

class validarlogin
{
    /**
     * @param array $input ['correo' => '...', 'contrasenia' => '...']
     * @return array ['correo' => 'mensaje', 'contrasenia' => 'mensaje'] (vacío si no hay errores)
     */
    public static function validarCampos(array $input): array
    {
        $errors = [];

        if (empty($input['correo'])) {
            $errors['correo'] = 'El correo es obligatorio.';
        } elseif (! filter_var($input['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'El correo no tiene formato válido.';
        }

        if (empty($input['contrasenia'])) {
            $errors['contrasenia'] = 'La contraseña es obligatoria.';
        } elseif (mb_strlen($input['contrasenia']) < 6) {
            $errors['contrasenia'] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        return $errors;
    }
}
