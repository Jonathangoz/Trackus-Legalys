<?php
# src/Modulos//Cobrocoactivo/Modelos/obligados_pagos.php
declare(strict_types=1);

namespace App\Modulos\Deudores\Modelos;

use PDO;

class obligados_pagos {
    protected PDO $db;

    public function __construct() {
        $this->db = \App\Comunes\DB\conexion::instanciaDB();
    }
    /**
     * Trae todos los deudores activos.
     * Retorna un array de arrays asociativos (sin exponer datos sensibles).
     */

    public function getUsuarios(): array {
        $stmt = $this->db->query("SELECT * FROM funcionarios ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  /*  public static function getDeudor(): array {
        $db = conexion::instanciaDB();
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
 /*   public static function activate(int $id): bool {
        $db = conexion::instanciaDB();
        $sql = "SELECT activar_deudor(:id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public static function deactivate(int $id): bool {
        $db = conexion::instanciaDB();
        $sql = "SELECT desactivar_deudor(:id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    } */
}