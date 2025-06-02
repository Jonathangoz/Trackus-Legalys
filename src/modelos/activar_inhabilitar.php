<?php
// src/modelos/activar_inhabilitar.php
declare(strict_types=1);

namespace App\modelos;

use App\DB\conexion;
use PDO;

class activar_inhabilitar
{
    /**
     * Trae todos los deudores activos.
     * Retorna un array de arrays asociativos (sin exponer datos sensibles).
     */
    public static function allActivos(): array
    {
        $db = conexion::getInstance();
        $sql = "SELECT id, nombre, deuda 
                  FROM funcionarios 
                 WHERE activo = TRUE";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Activa (o “reactiva”) un deudor. Supongamos que usas función almacenada:
     *   CREATE FUNCTION activar_deudor(int) RETURNS boolean AS $$
     *   BEGIN UPDATE deudores SET activo = TRUE WHERE id = $1; RETURN TRUE; END;$ LANGUAGE plpgsql;
     */
    public static function activate(int $id): bool
    {
        $db = conexion::getInstance();
        $sql = "SELECT activar_funcionario(:id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public static function deactivate(int $id): bool
    {
        $db = conexion::getInstance();
        $sql = "SELECT desactivar_funcionario(:id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
