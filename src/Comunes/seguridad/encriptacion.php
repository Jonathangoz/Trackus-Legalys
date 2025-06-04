<?php
// src/seguridad/encriptacion.php
#declare(strict_types=1);

namespace App\Comunes\seguridad;

class encriptacion
{
    /**
     * Genera 64 bytes aleatorios y devuelve su representación hexadecimal (128 caracteres).
     */
    public static function tokenRandom(): string
    {
        return bin2hex(random_bytes(64));
    }

    /**
     * Firma el token con HMAC-SHA256 usando SECRET_KEY.
     * Retorna la cadena: "<token>.<firma_hex>"
     */
    public static function lenToken(string $token): string
    {
        $secret = $_ENV['SECRET_KEY'] ?? '';
        if (strlen($secret) < 32) {
            throw new \RuntimeException('SECRET_KEY debe tener al menos 32 caracteres.');
        }

        $hmacBin = hash_hmac('sha256', $token, $secret, true);
        $hmacHex = bin2hex($hmacBin);

        return "{$token}.{$hmacHex}";
    }

    /**
     * Verifica que la firma HMAC sea válida.
     * Si es correcta, devuelve el token original; si falla, devuelve null.
     */
    public static function verificarToken(string $firma): ?string
    {
        $parts = explode('.', $firma, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$token, $firmaHex] = $parts;
        $secret = $_ENV['SECRET_KEY'] ?? '';
        if (strlen($secret) < 32) {
            return null;
        }

        $calcHmacBin = hash_hmac('sha256', $token, $secret, true);
        $calcHex     = bin2hex($calcHmacBin);

        if (hash_equals($calcHex, $firmaHex)) {
            return $token;
        }
        return null;
    }

    /**
     * Cifra un token usando AES-256-GCM.
     * Retorna la cadena: base64(iv) . "." . base64(ciphertext) . "." . base64(tag)
     */
    public static function encriptarToken(string $token): string
    {
        $key = $_ENV['SECRET_KEY2'] ?? '';
        if (strlen($key) < 32) {
            throw new \RuntimeException('SECRET_KEY2 debe tener al menos 32 caracteres.');
        }

        // IV de 12 bytes recomendado para AES-GCM
        $iv = random_bytes(12);
        $tag = '';

        $ciphertextBin = openssl_encrypt(
            $token,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',   // AAD vacío
            16    // longitud del tag en bytes
        );

        if ($ciphertextBin === false) {
            throw new \RuntimeException('Error al cifrar el token con AES-256-GCM.');
        }

        $ivB64     = base64_encode($iv);
        $cipherB64 = base64_encode($ciphertextBin);
        $tagB64    = base64_encode($tag);

        return "{$ivB64}.{$cipherB64}.{$tagB64}";
    }

    /**
     * Descifra un token cifrado con AES-256-GCM.
     * Recibe: "base64(iv).base64(ciphertext).base64(tag)".
     * Devuelve el token original o null si falla autenticación o descifrado.
     */
    public static function descencriptarToken(string $encriptar): ?string
    {
        $parts = explode('.', $encriptar, 3);
        if (count($parts) !== 3) {
            return null;
        }

        [$ivB64, $cipherB64, $tagB64] = $parts;
        $iv         = base64_decode($ivB64, true);
        $ciphertext = base64_decode($cipherB64, true);
        $tag        = base64_decode($tagB64, true);

        if ($iv === false || $ciphertext === false || $tag === false) {
            return null;
        }

        $key = $_ENV['SECRET_KEY2'] ?? '';
        if (strlen($key) < 32) {
            return null;
        }

        $textoPlano = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '' // AAD
        );

        return $textoPlano === false ? null : $textoPlano;
    }

    /**
     * Firma con HMAC y luego cifra con AES-GCM.
     * Devuelve: "ivB64.cipherB64.tagB64"
     */
    public static function verificarEncriptacion(string $token): string
    {
        $firma = self::lenToken($token);
        return self::encriptarToken($firma);
    }

    /**
     * Descifra y luego verifica HMAC.
     * Si todo es válido, devuelve el token original; si falla, devuelve null.
     */
    public static function descencrriptarVerificar(string $encriptar): ?string
    {
        $firma = self::descencriptarToken($encriptar);
        if ($firma === null) {
            return null;
        }
        return self::verificarToken($firma);
    }

    /**
     * Firma y cifra un token, y además guarda en sesión su fecha de expiración:
     * $_SESSION['token_expiry'] = now + $lifetime segundos.
     *
     * @param string $token    El token en texto claro (por ejemplo, 64 bytes hex)
     * @param int    $lifetime Cantidad de segundos que debe durar el token
     */
    public static function firmaEncriptadaExpiracion(string $token, int $lifetime): string
    {
        // 1) Firma
        $firma = self::lenToken($token);

        // 2) Cifra
        $encriptar = self::encriptarToken($firma);

        // 3) Inicia sesión si no está activa y guarda expiración en _SESSION
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
                'cookie_secure'   => true,
            ]);
        }
        $_SESSION['token_expiry'] = time() + $lifetime;

        return $encriptar;
    }

    /**
     * Antes de descifrar, comprueba si la sesión tiene $_SESSION['token_expiry'] y que no haya pasado.
     * Luego descifra y verifica HMAC. Devuelve el token original o null si expiró o es inválido.
     *
     * @param string $encriptar El token cifrado ("ivB64.cipherB64.tagB64")
     */
    public static function descencriptverificarExpiracion(string $encriptar): ?string
    {
        // 1) Verificar expiración desde $_SESSION
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
                'cookie_secure'   => true,
            ]);
        }

        $expiry = $_SESSION['token_expiry'] ?? 0;
        if (time() > intval($expiry)) {
            // Token expirado: destruir la sesión y devolver null
            $_SESSION = [];
            session_destroy();
            return null;
        }

        // 2) Descifrar (AES-GCM)
        $firma = self::descencriptarToken($encriptar);
        if ($firma === null) {
            return null;
        }

        // 3) Verificar firma HMAC
        return self::verificarToken($firma);
    }
}
