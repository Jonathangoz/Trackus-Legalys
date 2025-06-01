<?php
// src/Services/CryptoService.php
declare(strict_types=1);

namespace App\servicios;

class encriptacion
{
    /**
     * Genera 64 bytes aleatorios y devuelve su representación hexadecimal (128 caracteres).
     */
    public static function generateRandomToken(): string
    {
        return bin2hex(random_bytes(64));
    }

    /**
     * Firma el token con HMAC-SHA256 usando SECRET_KEY, y concatena token y firma: "<token>.<firma_hex>"
     */
    public static function signToken(string $token): string
    {
        $secret = $_ENV['SECRET_KEY'];
        $hmac   = hash_hmac('sha256', $token, $secret, true);
        $hmacHex = bin2hex($hmac);
        return "{$token}.{$hmacHex}";
    }

    /**
     * Verifica que la firma sea válida.
     * Si OK, devuelve el token original; si falla, devuelve null.
     */
    public static function verifySignedToken(string $signed): ?string
    {
        $parts = explode('.', $signed);
        if (count($parts) !== 2) {
            return null;
        }
        [$token, $hmacHex] = $parts;
        $secret = $_ENV['SECRET_KEY'];
        $calc = hash_hmac('sha256', $token, $secret, true);
        $calcHex = bin2hex($calc);

        if (hash_equals($calcHex, $hmacHex)) {
            return $token;
        }
        return null;
    }
}
