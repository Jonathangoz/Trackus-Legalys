<?php

namespace App\Comunes\seguridad;

class csrf
{
    public static function generateToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if ( ! hash_equals($_SESSION['csrf_token'], $token) ) {
                throw new \Exception("CSRF token inválido");
            }
        }
    }

    public static function insertHiddenInput(): void {
        $token = self::generateToken();
        echo '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($token).'">';
    }
}
