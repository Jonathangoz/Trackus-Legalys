<?php
// src/DB/conexion.php
declare(strict_types=1);

namespace App\DB;

require_once __DIR__ . '../../config/env.php';

/**
 * Devuelve una instancia PDO única (patrón singleton).
 */

use PDO;
use PDOException;

class conexion
{
    private static ?PDO $instance = null;

    private function __construct() { }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'] ?? '5432';
            $db   = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";

            try {
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                $pdo->exec("SET TIME ZONE 'UTC'");
                self::$instance = $pdo;
            } catch (PDOException $e) {
                error_log("Error en conexión PDO: " . $e->getMessage());
                throw new \RuntimeException("No se pudo conectar a la base de datos.");
            }
        }

        return self::$instance;
    }
}