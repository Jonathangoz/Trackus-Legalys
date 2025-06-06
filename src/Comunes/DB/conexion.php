<?php
// src/Comunes/DB/conexion.php
#declare(strict_types=1);

namespace App\Comunes\DB;

require_once __DIR__ . '/../../../config/env.php';

/**
 * Devuelve una instancia PDO única (patrón singleton).
 */

use PDO;
use PDOException;

class conexion
{
    private static ?PDO $instancia = null;

    private function __construct() { }

    public static function instanciaDB(): PDO {
        if (self::$instancia === null) {
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'] ?? '5432';
            $db   = $_ENV['DB_DATABASE'];
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];

            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";

            try {
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                $pdo->exec("SET TIME ZONE 'UTC'");
                self::$instancia = $pdo;
            } catch (PDOException $e) {
                error_log("Error en conexión PDO: " . $e->getMessage());
                throw new \RuntimeException("No se pudo conectar a la base de datos.");
            }
        }

        return self::$instancia;
    }
}