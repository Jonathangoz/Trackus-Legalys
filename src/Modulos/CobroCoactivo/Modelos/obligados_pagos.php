<?php
// src/modelos/obligados_pagos.php
declare(strict_types=1);

namespace App\Modulos\CobroCoactivo\Modelos;

use App\Comunes\DB\conexion;
use PDO;

class obligados_pagos
{
    /**
     * Trae todos los deudores activos.
     * Retorna un array de arrays asociativos (sin exponer datos sensibles).
     */
    public static function allActivos(): array
    {
        $db = conexion::getInstance();
        $sql = "SELECT id, nombre, deuda 
                  FROM deudores 
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
        $sql = "SELECT activar_deudor(:id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public static function deactivate(int $id): bool
    {
        $db = conexion::getInstance();
        $sql = "SELECT desactivar_deudor(:id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
