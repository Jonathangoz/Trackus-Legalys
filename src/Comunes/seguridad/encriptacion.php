<?php
// src/Comunes/seguridad/encriptacion.php (realiza token para csrf, y token para usuario con JWT + JWE + AES-GCM (cifra encabezados))
declare(strict_types=1);

namespace App\Comunes\seguridad;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class encriptacion {
    private static Logger $logger;

    private static function initLogger(): void {
        if (!isset(self::$logger)) {
            self::$logger = loggers::createLogger();
            self::$logger->info("🛡 encriptacion::initLogger() inicializado");
        }
    }

    # Retorna clave de firma (HMAC) en binario decodificado de base64, verifica longitud minima, y que este con base64 mas segura.
    private static function getSigningKey(): string {
        self::initLogger();
        $raw = $_ENV['SECRET_KEY'] ?? '';
        if (!str_starts_with($raw, 'base64:')) {
            self::$logger->error("🚨 getSigningKey(): SECRET_KEY debe tener prefijo base64:");
            throw new \RuntimeException("SECRET_KEY debe tener prefijo base64:");
        }
        $bin = base64_decode(substr($raw, 7), true);
        if ($bin === false || strlen($bin) !== 32) {
            self::$logger->error("🚨 getSigningKey(): SECRET_KEY inválida o tamaño incorrecto");
            throw new \RuntimeException("SECRET_KEY debe ser base64 de 32 bytes");
        }
        return $bin;
    }

    # Retorna clave de cifrado (AES) en binario decodificado de base64.verifica longitud minima, y que este con base64 mas segura.
    private static function getEncryptionKey(): string {
        self::initLogger();
        $raw = $_ENV['SECRET_KEY2'] ?? '';
        if (!str_starts_with($raw, 'base64:')) {
            self::$logger->error("🚨 getEncryptionKey(): SECRET_KEY2 debe tener prefijo base64:");
            throw new \RuntimeException("SECRET_KEY2 debe tener prefijo base64:");
        }
        $bin = base64_decode(substr($raw, 7), true);
        if ($bin === false || strlen($bin) !== 32) {
            self::$logger->error("🚨 getEncryptionKey(): SECRET_KEY2 inválida o tamaño incorrecto");
            throw new \RuntimeException("SECRET_KEY2 debe ser base64 de 32 bytes");
        }
        return $bin;
    }

    # Genera token cifrado base64, uso exclusivo para token csrf.
    public static function tokenRandom(): string {
        self::initLogger();
        $random = base64_encode(random_bytes(32));
        self::$logger->debug("🌀 tokenRandom() generado: {$random}");
        return $random;
    }

    /**
     * Genera un JWT firmado con HMAC-SHA256.
     * @param array $customClaims  – Debe incluir 'user_id' y 'tipo_rol'.
     * @param int   $lifetime      – Segundos de vida del token.
     * @return string JWT compacto.
     */
    public static function generarJwt(array $customClaims, int $lifetime = 600): string {
        self::initLogger();
        $signKey = self::getSigningKey();
        $now     = time();

        $payload = [
            'iss'      => $_ENV['APP_URL'] ?? 'tu-app',
            'iat'      => $now,
            'exp'      => $now + $lifetime,
            'user_id'  => $customClaims['user_id'],
            'tipo_rol' => $customClaims['tipo_rol'],
        ];

        $jwt = JWT::encode($payload, $signKey, 'HS256');
        self::$logger->info("✅ generarJwt() JWT: {$jwt}");

        # Guardar expiración en sesión
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => false,
            ]);
            self::$logger->debug("➰ session_start() en generarJwt()");
        }
        $_SESSION['token_expiry'] = time() + $lifetime;
        self::$logger->info("⏳ token_expiry guardado en" . $_SESSION['token_expiry']);
        
        return $jwt;
    }

    # Verifica y decodifica un JWT. Retorna claims o null en error.
    public static function validarJwt(string $jwt_compact): ?array {
        self::initLogger();
        $signKey = self::getSigningKey();
        try {
            $decoded = JWT::decode($jwt_compact, new Key($signKey, 'HS256'));
            $claims  = (array) $decoded;
            self::$logger->info("✅ validarJwt() OK, claims: " . json_encode($claims));
            return $claims;
        } catch (\Exception $e) {
            self::$logger->warning("❌ validarJwt() falló: " . $e->getMessage());
            return null;
        }
    }

    # Cifra texto con AES-256-GCM. Retorna “ivB64.cipherB64.tagB64”.
    public static function aesGcmEncrypt(string $plaintext): string {
        self::initLogger();
        $key = self::getEncryptionKey();
        $iv  = random_bytes(12);
        $tag = '';

        $cipherBin = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16,
        );
        if ($cipherBin === false) {
            self::$logger->error("🚨 aesGcmEncrypt(): openssl_encrypt devolvió false");
            throw new \RuntimeException("Error en AES-GCM encrypt.");
        }

        $ivB64     = base64_encode($iv);
        $cipherB64 = base64_encode($cipherBin);
        $tagB64    = base64_encode($tag);
        $jwe       = "{$ivB64}.{$cipherB64}.{$tagB64}";

        self::$logger->info("✅ aesGcmEncrypt() JWE: {$jwe}");
        return $jwe;
    }

    # Descifra un JWE AES-256-GCM. Retorna plaintext o null si falla.
    public static function aesGcmDecrypt(string $jwe_compact): ?string {
        self::initLogger();
        $parts = explode('.', $jwe_compact, 3);
        if (count($parts) !== 3) {
            self::$logger->warning("⚠️ aesGcmDecrypt(): formato JWE inválido");
            return null;
        }
        [$ivB64, $cipherB64, $tagB64] = $parts;
        $iv     = base64_decode($ivB64, true);
        $cipher = base64_decode($cipherB64, true);
        $tag    = base64_decode($tagB64, true);
        if ($iv === false || $cipher === false || $tag === false) {
            self::$logger->warning("⚠️ aesGcmDecrypt(): error base64_decode");
            return null;
        }

        $key = self::getEncryptionKey();
        $plaintext = openssl_decrypt(
            $cipher,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
        );
        if ($plaintext === false) {
            self::$logger->warning("❌ aesGcmDecrypt(): autenticación o descifrado falló");
            return null;
        }
        self::$logger->info("✅ aesGcmDecrypt() devolvió JWT: {$plaintext}");
        return $plaintext;
    }

    # Genera un JWE que contiene el JWT firmado para user_id y tipo_rol.
    public static function generarJwe(array $customClaims, int $lifetime = 600): string {
        self::initLogger();
        $jwt = self::generarJwt($customClaims, $lifetime);
        return self::aesGcmEncrypt($jwt);
    }

    # Valida un JWE: descifra → verifica JWT → retorna claims o null.
    public static function validarJwe(string $jwe_compact): ?array {
        self::initLogger();
        $jwt = self::aesGcmDecrypt($jwe_compact);
        if ($jwt === null) {
            return null;
        }
        return self::validarJwt($jwt);
    }
}