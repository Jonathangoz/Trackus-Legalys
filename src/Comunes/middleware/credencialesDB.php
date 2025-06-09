<?php
# src/Comunes/middleware/credencialesDB.php (Valida datos escritos en el formulario login con la base de datos)
declare(strict_types=1);

namespace App\Comunes\middleware;

use App\Comunes\DB\conexion;
use App\Comunes\utilidades\loggers;
use Monolog\Logger;
use PDO;
use PDOException;

class credencialesDB {

    # variables definidas para uso en el sistema, basados con lo pedido en la base de datos
    public int    $id;
    public string $tipo_usuario;   // 'funcionario' o 'usuario'
    public string $correo;
    public string $correo_institucional;
    public string $password_hash; 
    public string $nombres;
    public string $apellidos;
    public string $tipo_rol;

    /** @var Logger */
    private static Logger $logger;

    # Inicializa logger
    private static function initLogger(): void {
        if (!isset(self::$logger)) {
            self::$logger = loggers::createLogger();
            self::$logger->info("💼 credencialesDB::initLogger() inicializado");
        }
    }

    # Construye la instancia a partir de un array (columnas en la BD).
    public function __construct(array $row) {
        $this->id                   = intval($row['id'] ?? 0);
        $this->tipo_usuario         = $row['tipo_usuario'] ?? ''; // 'funcionario' o 'usuario'
        $this->correo               = $row['correo'] ?? '';
        $this->correo_institucional = $row['correo_institucional'] ?? '';
        $this->password_hash        = $row['password_hash'] ?? '';
        $this->nombres              = $row['nombres'] ?? '';
        $this->apellidos            = $row['apellidos'] ?? '';
        $this->tipo_rol             = $row['tipo_rol'] ?? '';

        self::initLogger();
        self::$logger->info("📦 credencialesDB::__construct() creado para user_id={$this->id}" . "correo={$this->correo}, correo_institucional={$this->correo_institucional}");
    }

    # Busca un usuario activo (funcionario o deudor) por correo. Retorna null si no existe ninguno.
    public static function credenciales(string $email, string $password_hash): ?self {
        self::initLogger();
        self::$logger->info("🔐 credencialesDB::credenciales() invocado para correo: {$email}");

        # Peticion query hacia la base de
        $db = conexion::instanciaDB();
        $sql = "SELECT tipo_usuario, 
                    id,
                    nombres,
                    apellidos,
                    correo_institucional,
                    correo,
                    password_hash,
                    tipo_rol
                FROM (
                    SELECT 1 AS prioridad,
                        'funcionarios' AS tipo_usuario,
                        f.id             AS id,
                        f.nombres,
                        f.apellidos,
                        f.correo_institucional,
                        f.correo,
                        f.password_hash,
                        r.nombre         AS tipo_rol
                    FROM funcionarios AS f
                    INNER JOIN roles AS r ON f.rol_id = r.id
                    WHERE f.correo_institucional = :correo
                        OR f.correo = :correo
                    UNION ALL
                    SELECT 2 AS prioridad,
                        'deudores' AS tipo_usuario,
                        d.id             AS id,
                        d.nombres,
                        d.apellidos,
                        NULL             AS correo_institucional,
                        d.correo,
                        d.password_hash,
                        r.nombre         AS tipo_rol
                    FROM deudores AS d
                    INNER JOIN roles AS r ON d.rol_id = r.id
                    WHERE d.correo = :correo
                ) AS sub
                ORDER BY prioridad
                LIMIT 1";

        try {
            self::$logger->debug("📋 Preparando consulta SQL en credencialesDB");
            $stmt = $db->prepare($sql);
            $stmt->execute(['correo' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (! $row) {
                self::$logger->warning("❌ credencialesDB: No se encontró usuario para correo: {$email}.{$password_hash}");
                return null;
            }
            self::$logger->debug("▶ credencialesDB::credenciales() devolvió fila de BD: " . json_encode($row));
            self::$logger->info("✅ credencialesDB: Usuario encontrado, ID={$row['tipo_usuario']}, tipo_usuario={$row['tipo_rol']}");
            return new self($row);
        } catch (PDOException $e) {
            self::$logger->error("🚨 credencialesDB::credenciales Error PDO: " . $e->getMessage());
            return null;
        }
    }
}
