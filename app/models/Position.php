<?php

namespace App\Models;

class Position extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM positions ORDER BY position_name ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO positions (position_name, max_votes) VALUES (?, ?)");
        $stmt->execute([
            $data['position_name'],
            $data['max_votes'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $positionId, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE positions SET position_name = ?, max_votes = ? WHERE position_id = ?");
        return $stmt->execute([
            $data['position_name'],
            $data['max_votes'],
            $positionId
        ]);
    }

    public function delete(int $positionId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM positions WHERE position_id = ?");
        return $stmt->execute([$positionId]);
    }

    public function find(int $positionId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM positions WHERE position_id = ?");
        $stmt->execute([$positionId]);
        return $stmt->fetch() ?: null;
    }

    public function existsName(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM positions WHERE position_name = ?";
        $params = [$name];

        if ($excludeId !== null) {
            $sql .= " AND position_id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
