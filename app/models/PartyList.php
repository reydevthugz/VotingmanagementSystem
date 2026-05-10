<?php

namespace App\Models;

class PartyList extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM party_lists ORDER BY party_name ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO party_lists (party_name, description) VALUES (?, ?)");
        $stmt->execute([
            $data['party_name'],
            $data['description'] ?? ''
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $partyId, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE party_lists SET party_name = ?, description = ? WHERE party_id = ?");
        return $stmt->execute([
            $data['party_name'],
            $data['description'],
            $partyId
        ]);
    }

    public function delete(int $partyId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM party_lists WHERE party_id = ?");
        return $stmt->execute([$partyId]);
    }

    public function find(int $partyId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM party_lists WHERE party_id = ?");
        $stmt->execute([$partyId]);
        return $stmt->fetch() ?: null;
    }

    public function existsName(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM party_lists WHERE party_name = ?";
        $params = [$name];

        if ($excludeId !== null) {
            $sql .= " AND party_id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
