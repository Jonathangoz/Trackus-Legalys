<?php
// src/Comunes/middleware/credencialesDB.php
#declare(strict_types=1);

namespace App\Comunes\middleware;

use App\Comunes\DB\conexion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;
use PDO;
use PDOException;

class credencialesDB
{
    public int    $id;
    public string $tipo_usuario;   // 'funcionario' o 'usuario'
    public string $correo;
    public string $password_hash;  // columna "contrasenia"
    public string $nombres;
    public string $apellidos;
    public string $tipo_rol;

    /** @var Logger */
    private static Logger $logger;

    /**
     * Inicializa el logger si aún no existe
     */
    private static function initLogger(): void {
        if (!isset(self::$logger)) {
            self::$logger = loggers::createLogger();
            self::$logger->info("💼 credencialesDB::initLogger() inicializado");
        }
    }

    /**
     * Construye la instancia a partir de un array (fila de BD).
     */
    public function __construct(array $row) {
        $this->tipo_usuario  = $row['tipo_usuario']; // 'funcionario' o 'usuario'
        $this->id            = (int) $row['id'];
        $this->correo        = $row['correo'];
        $this->password_hash = $row['contrasenia'];
        $this->nombres       = $row['nombres'];
        $this->apellidos     = $row['apellidos'];
        $this->tipo_rol      = $row['tipo_rol'];

        self::initLogger();
        self::$logger->info("📦 credencialesDB::__construct() creado para user_id={$this->id}, correo={$this->correo}");
    }

    /**
     * Busca un usuario activo (funcionario o usuario) por correo.
     * Retorna null si no existe ninguno.
     */
    public static function credenciales(string $email, string $password): ?self {
        self::initLogger();
        self::$logger->info("🔐 credencialesDB::credenciales() invocado para correo: {$email}");

        $db = conexion::instanciaDB();
        $sql = "
            SELECT 'funcionario' AS tipo_usuario, id_funcionario AS id, nombres, apellidos, correo, contrasenia, tipo_rol
              FROM funcionarios
             WHERE correo = :correo
               
            UNION ALL
            SELECT 'usuario' AS tipo_usuario, id_usuario AS id, nombres, apellidos, correo, contrasenia, tipo_rol
              FROM usuarios
             WHERE correo = :correo
               
             LIMIT 1
        ";

        try {
            self::$logger->debug("📋 Preparando consulta SQL en credencialesDB");
            $stmt = $db->prepare($sql);
            $stmt->execute(['correo' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (! $row) {
                self::$logger->warning("❌ credencialesDB: No se encontró usuario para correo: {$email}");
                return null;
            }
            self::$logger->info("✅ credencialesDB: Usuario encontrado, ID={$row['id']}, tipo_usuario={$row['tipo_rol']}");
            return new self($row);
        } catch (PDOException $e) {
            self::$logger->error("🚨 credencialesDB::credenciales Error PDO: " . $e->getMessage());
            return null;
        }
    }
}
