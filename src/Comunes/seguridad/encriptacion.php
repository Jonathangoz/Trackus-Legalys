<?php
// src/Comunes/seguridad/encriptacion.php
#declare(strict_types=1);

namespace App\Comunes\seguridad;

use App\Comunes\utilidades\loggers;
use Monolog\Logger;

class encriptacion
{
    /** @var Logger */
    private static Logger $logger;

    /**
     * Inicializa el logger si aún no existe
     */
    private static function initLogger(): void {
        if (!isset(self::$logger)) {
            self::$logger = loggers::createLogger();
            self::$logger->info("🛡 encriptacion::initLogger() inicializado");
        }
    }

    /**
     * Genera 64 bytes aleatorios y devuelve su representación hexadecimal (128 caracteres).
     */
    public static function tokenRandom(): string
    {
        self::initLogger();
        $random = bin2hex(random_bytes(64));
        self::$logger->debug("🌀 tokenRandom() generado: {$random}");
        return $random;
    }

    /**
     * Firma el token con HMAC-SHA256 usando SECRET_KEY.
     * Retorna la cadena: "<token>.<firma_hex>"
     */
    public static function lenToken(string $token): string {
        self::initLogger();
        $secret = $_ENV['SECRET_KEY'] ?? '';
        self::$logger->debug("🔑 lenToken() usando token: {$token}");
        if (strlen($secret) < 32) {
            self::$logger->error("🚨 lenToken(): SECRET_KEY inválida o demasiado corta");
            throw new \RuntimeException('SECRET_KEY debe tener al menos 32 caracteres.');
        }

        $hmacBin = hash_hmac('sha256', $token, $secret, true);
        $hmacHex = bin2hex($hmacBin);
        $firma = "{$token}.{$hmacHex}";
        self::$logger->info("✅ lenToken() produjo firma HMAC: {$firma}");
        return $firma;
    }

    /**
     * Verifica que la firma HMAC sea válida.
     * Si es correcta, devuelve el token original; si falla, devuelve null.
     */
    public static function verificarToken(string $firma): ?string {
        self::initLogger();
        self::$logger->info("🔍 verificarToken() recibido: {$firma}");
        $parts = explode('.', $firma, 2);
        if (count($parts) !== 2) {
            self::$logger->warning("⚠️ verificarToken(): formato incorrecto");
            return null;
        }

        [$token, $firmaHex] = $parts;
        $secret = $_ENV['SECRET_KEY'] ?? '';
        if (strlen($secret) < 32) {
            self::$logger->warning("⚠️ verificarToken(): SECRET_KEY inválida o demasiado corta");
            return null;
        }

        $calcHmacBin = hash_hmac('sha256', $token, $secret, true);
        $calcHex     = bin2hex($calcHmacBin);
        if (hash_equals($calcHex, $firmaHex)) {
            self::$logger->info("✅ verificarToken(): firma válida, token extraído: {$token}");
            return $token;
        }
        self::$logger->warning("❌ verificarToken(): firma no coincide");
        return null;
    }

    /**
     * Cifra un token usando AES-256-GCM.
     * Retorna la cadena: base64(iv) . "." . base64(ciphertext) . "." . base64(tag)
     */
    public static function encriptarToken(string $token): string
    {
        self::initLogger();
        $key = $_ENV['SECRET_KEY2'] ?? '';
        self::$logger->debug("🔐 encriptarToken() token a cifrar: {$token}");
        if (strlen($key) < 32) {
            self::$logger->error("🚨 encriptarToken(): SECRET_KEY2 inválida o demasiado corta");
            throw new \RuntimeException('SECRET_KEY2 debe tener al menos 32 caracteres.');
        }

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
            self::$logger->error("🚨 encriptarToken(): error en openssl_encrypt");
            throw new \RuntimeException('Error al cifrar el token con AES-256-GCM.');
        }

        $ivB64     = base64_encode($iv);
        $cipherB64 = base64_encode($ciphertextBin);
        $tagB64    = base64_encode($tag);
        $resultado = "{$ivB64}.{$cipherB64}.{$tagB64}";
        self::$logger->info("✅ encriptarToken() produjo resultado: {$resultado}");
        return $resultado;
    }

    /**
     * Descifra un token cifrado con AES-256-GCM.
     * Devuelve el token original o null si falla autenticación o descifrado.
     */
    public static function descencriptarToken(string $encriptar): ?string
    {
        self::initLogger();
        self::$logger->info("🔍 descencriptarToken() recibido: {$encriptar}");
        $parts = explode('.', $encriptar, 3);
        if (count($parts) !== 3) {
            self::$logger->warning("⚠️ descencriptarToken(): formato inválido");
            return null;
        }

        [$ivB64, $cipherB64, $tagB64] = $parts;
        $iv         = base64_decode($ivB64, true);
        $ciphertext = base64_decode($cipherB64, true);
        $tag        = base64_decode($tagB64, true);

        if ($iv === false || $ciphertext === false || $tag === false) {
            self::$logger->warning("⚠️ descencriptarToken(): fallo al decodificar base64");
            return null;
        }

        $key = $_ENV['SECRET_KEY2'] ?? '';
        if (strlen($key) < 32) {
            self::$logger->warning("⚠️ descencriptarToken(): SECRET_KEY2 inválida o demasiado corta");
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

        if ($textoPlano === false) {
            self::$logger->warning("❌ descencriptarToken(): autenticación o descifrado falló");
            return null;
        }

        self::$logger->info("✅ descencriptarToken() devolvió texto plano: {$textoPlano}");
        return $textoPlano;
    }

    /**
     * Firma con HMAC y luego cifra con AES-GCM.
     * Devuelve: "ivB64.cipherB64.tagB64"
     */
    public static function verificarEncriptacion(string $token): string
    {
        self::initLogger();
        self::$logger->info("🔐 verificarEncriptacion() recibe token: {$token}");
        $firma = self::lenToken($token);
        self::$logger->debug("🔑 verificarEncriptacion() firma intermedia: {$firma}");
        $encriptar = self::encriptarToken($firma);
        self::$logger->info("✅ verificarEncriptacion() resultado cifrado: {$encriptar}");
        return $encriptar;
    }

    /**
     * Descifra y luego verifica HMAC.
     * Si todo es válido, devuelve el token original; si falla, devuelve null.
     */
    public static function descencrriptarVerificar(string $encriptar): ?string
    {
        self::initLogger();
        self::$logger->info("🔍 descencrriptarVerificar() recibe: {$encriptar}");
        $firma = self::descencriptarToken($encriptar);
        if ($firma === null) {
            self::$logger->warning("⚠️ descencrriptarVerificar(): descencriptarToken retornó null");
            return null;
        }
        $token = self::verificarToken($firma);
        if ($token === null) {
            self::$logger->warning("⚠️ descencrriptarVerificar(): verificarToken falló");
            return null;
        }
        self::$logger->info("✅ descencrriptarVerificar() devolvió token válido: {$token}");
        return $token;
    }

    /**
     * Firma y cifra un token, y además guarda en sesión su fecha de expiración.
     * @param string $token
     * @param int    $lifetime
     */
    public static function firmaEncriptadaExpiracion(string $token, int $lifetime): string {
        self::initLogger();
        self::$logger->info("🔑 firmaEncriptadaExpiracion() token: {$token}, lifetime: {$lifetime}");

        // 1) Firma
        $firma = self::lenToken($token);
        self::$logger->debug("🔐 Token firmado: {$firma}");

        // 2) Cifra la firma
        $encriptar = self::encriptarToken($firma);
        self::$logger->debug("🔒 Firma cifrada (encriptar): {$encriptar}");

        // 3) Guardar expiración en sesión
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => false,
            ]);
            self::$logger->debug("➰ session_start() en firmaEncriptadaExpiracion()");
        }
        $_SESSION['token_expiry'] = time() + $lifetime;
        self::$logger->info("⏳ token_expiry guardado en" . $_SESSION['token_expiry']);

        return $encriptar;
    }

    /**
     * Antes de descifrar, comprueba expiración y verifica HMAC.
     * @param string $encriptar
     */
    public static function descencriptverificarExpiracion(string $encriptar): ?string
    {
        self::initLogger();
        self::$logger->info("⏱ descencriptverificarExpiracion() recibido: {$encriptar}");

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => false,
            ]);
            self::$logger->debug("➰ session_start() en descencriptverificarExpiracion()");
        }

        $expiry = $_SESSION['token_expiry'] ?? 0;
        self::$logger->debug("🗓 token_expiry en sesión: {$expiry}, ahora: {time()}");
        if (time() > intval($expiry)) {
            self::$logger->warning("⌛ Token expirado, destruyendo sesión");
            $_SESSION = [];
            session_destroy();
            return null;
        }

        $firma = self::descencriptarToken($encriptar);
        if ($firma === null) {
            self::$logger->warning("❌ descencriptverificarExpiracion(): descencriptarToken falló");
            return null;
        }

        $token = self::verificarToken($firma);
        if ($token === null) {
            self::$logger->warning("❌ descencriptverificarExpiracion(): verificarToken falló");
            return null;
        }

        self::$logger->info("✅ descencriptverificarExpiracion() devolvió token válido: {$token}");
        return $token;
    }
}
