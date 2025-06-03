<?php
// src/Utils/RateLimiterPg.php

namespace App\servicios;

use PDO;
use DateTime;
use Exception;

class limitarPeticiones
{
    private PDO $pdo;
    private int $maxRequests;
    private int $windowMinutes;

    public function __construct(PDO $pdo)
    {
        $this->pdo           = $pdo;
        $this->maxRequests   = intval(getenv('RATE_LIMIT_REQUESTS') ?: 60);
        $this->windowMinutes = intval(getenv('RATE_LIMIT_MINUTES') ?: 1);
    }

    /**
     * Intenta registrar una petición para $identifier.
     * @param string $identifier   // user_id o token
     * @return bool true si aún está dentro del límite, false si ya lo excedió
     * @throws Exception en error de DB
     */
    public function allowRequest(string $identifier): bool
    {
        $now = new DateTime();

        // Normalizamos la hora al inicio del minuto actual:
        // Si ahora es “2025-05-31 14:23:45”, windowStart = “2025-05-31 14:23:00”
        $windowStart = (clone $now)->setTime(
            intval($now->format('H')),
            intval($now->format('i')),
            0
        );

        // Insertamos o actualizamos la fila para este identificador y ventana:
        $sql = "
            INSERT INTO api_rate_limits (identifier, window_start, request_count)
            VALUES (:identifier, :window_start, 1)
            ON CONFLICT (identifier, window_start)
            DO UPDATE SET request_count = api_rate_limits.request_count + 1
            RETURNING request_count
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':identifier'   => $identifier,
            ':window_start' => $windowStart->format('Y-m-d H:i:s'),
        ]);
        $count = intval($stmt->fetchColumn());

        // Si el contador supera el máximo, bloqueamos
        return $count <= $this->maxRequests;
    }

    /**
     * Opcional: ¿cuántas peticiones le quedan a $identifier antes de bloquearse?
     * @param string $identifier
     * @return int restante (0 si ya está bloqueado)
     */
    public function remainingRequests(string $identifier): int
    {
        $now = new DateTime();
        $windowStart = (clone $now)->setTime(
            intval($now->format('H')),
            intval($now->format('i')),
            0
        );

        $sql = "
            SELECT request_count
            FROM api_rate_limits
            WHERE identifier = :identifier
              AND window_start = :window_start
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':identifier'   => $identifier,
            ':window_start' => $windowStart->format('Y-m-d H:i:s'),
        ]);
        $count = intval($stmt->fetchColumn() ?: 0);
        $remain = intval(getenv('RATE_LIMIT_REQUESTS')) - $count;
        return ($remain > 0) ? $remain : 0;
    }
}
