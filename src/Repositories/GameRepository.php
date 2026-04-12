<?php
declare(strict_types=1);

namespace Anderson\XboxLive\Repositories;

use Anderson\XboxLive\Core\Database;
use PDO;

class GameRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca IDs de jogos com paginação real no SQL
     */
    public function getPagedIds(string $tableName, int $page, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Contagem total
        $totalStmt = $this->db->query("SELECT COUNT(*) FROM $tableName");
        $totalItems = (int)$totalStmt->fetchColumn();
        $totalPages = (int)ceil($totalItems / $perPage);

        // Busca paginada
        $stmt = $this->db->prepare("SELECT game_id FROM $tableName LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'ids' => $stmt->fetchAll(PDO::FETCH_COLUMN),
            'totalPages' => $totalPages,
            'totalItems' => $totalItems
        ];
    }

    public function syncIds(string $tableName, array $ids): int
    {
        $added = 0;
        $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM $tableName WHERE game_id = :id");
        $insertStmt = $this->db->prepare("INSERT INTO $tableName (game_id) VALUES (:id)");

        foreach ($ids as $id) {
            $checkStmt->execute(['id' => $id]);
            if (!(bool)$checkStmt->fetchColumn()) {
                $insertStmt->execute(['id' => $id]);
                $added++;
            }
        }
        return $added;
    }
}
