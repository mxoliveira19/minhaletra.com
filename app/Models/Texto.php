<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Texto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Get all published texts of a specific type.
     * Ordered by:
     * 1. modo = 'aleatorio' first, then modo = 'fixo'
     * 2. peso ASC
     */
    public function allPublic(string $tipo): array
    {
        // Normalize 'poesias' to 'poesia' for database
        $tipoQuery = ($tipo === 'poesias') ? 'poesia' : $tipo;

        $sql = "SELECT * FROM `textos` 
                WHERE `tipo` = :tipo AND `status` = 'publicado'
                ORDER BY FIELD(`modo`, 'aleatorio', 'fixo') ASC, `peso` ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipoQuery]);
        return $stmt->fetchAll();
    }

    /**
     * Get all texts of a specific type and status (published vs draft) for the admin dashboard.
     * Ordered by:
     * 1. modo = 'aleatorio' first, then modo = 'fixo'
     * 2. peso ASC
     */
    public function allAdmin(string $tipo, string $statusFilter = 'ativos'): array
    {
        // Normalize 'poesias' to 'poesia' for database
        $tipoQuery = ($tipo === 'poesias') ? 'poesia' : $tipo;
        
        $statusVal = ($statusFilter === 'rascunho') ? 'rascunho' : 'publicado';

        $sql = "SELECT * FROM `textos` 
                WHERE `tipo` = :tipo AND `status` = :status
                ORDER BY FIELD(`modo`, 'aleatorio', 'fixo') ASC, `peso` ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipoQuery, 'status' => $statusVal]);
        return $stmt->fetchAll();
    }

    /**
     * Count admin texts of a specific type and status.
     */
    public function countAdminByStatus(string $tipo, string $status): int
    {
        // Normalize 'poesias' to 'poesia' for database
        $tipoQuery = ($tipo === 'poesias') ? 'poesia' : $tipo;

        $sql = "SELECT COUNT(*) FROM `textos`
                WHERE `tipo` = :tipo AND `status` = :status";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipoQuery, 'status' => $status]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get a text by its ID.
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM `textos` WHERE `id` = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    /**
     * Increment and return the current joinha total for a published text.
     */
    public function incrementJoinhas(int $id): ?int
    {
        $sql = "UPDATE `textos`
                SET `joinhas_count` = `joinhas_count` + 1
                WHERE `id` = :id AND `status` = 'publicado'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() !== 1) {
            return null;
        }

        $countStmt = $this->db->prepare('SELECT `joinhas_count` FROM `textos` WHERE `id` = :id LIMIT 1');
        $countStmt->execute(['id' => $id]);

        return (int)$countStmt->fetchColumn();
    }

    /**
     * Save a text (insert new or update existing).
     */
    public function save(array $data): bool
    {
        if (isset($data['id']) && $data['id'] > 0) {
            // Update
            $sql = "UPDATE `textos` SET 
                        `tipo` = :tipo,
                        `modo` = :modo,
                        `titulo` = :titulo,
                        `conteudo` = :conteudo,
                        `peso` = :peso,
                        `status` = :status
                    WHERE `id` = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'tipo' => $data['tipo'],
                'modo' => $data['modo'],
                'titulo' => $data['titulo'],
                'conteudo' => $data['conteudo'],
                'peso' => (int)$data['peso'],
                'status' => $data['status'] ?? 'publicado',
                'id' => (int)$data['id']
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO `textos` (`tipo`, `modo`, `titulo`, `conteudo`, `peso`, `status`, `data_publicacao`) 
                    VALUES (:tipo, :modo, :titulo, :conteudo, :peso, :status, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'tipo' => $data['tipo'],
                'modo' => $data['modo'],
                'titulo' => $data['titulo'],
                'conteudo' => $data['conteudo'],
                'peso' => (int)$data['peso'],
                'status' => $data['status'] ?? 'publicado'
            ]);
        }
    }

    /**
     * Update the status of a text.
     */
    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE `textos` SET `status` = :status WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Delete a text permanently.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `textos` WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
