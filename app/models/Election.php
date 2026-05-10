<?php

namespace App\Models;

class Election extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM elections ORDER BY start_date DESC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO elections (title, start_date, end_date, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['start_date'],
            $data['end_date'],
            $data['status'] ?? 'inactive'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $electionId, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE elections SET title = ?, start_date = ?, end_date = ?, status = ? WHERE election_id = ?");
        return $stmt->execute([
            $data['title'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $electionId
        ]);
    }

    public function delete(int $electionId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM elections WHERE election_id = ?");
        return $stmt->execute([$electionId]);
    }

    public function find(int $electionId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM elections WHERE election_id = ?");
        $stmt->execute([$electionId]);
        return $stmt->fetch() ?: null;
    }

    public function deactivateAll(): bool
    {
        $stmt = $this->db->prepare("UPDATE elections SET status = 'inactive' WHERE status = 'active'");
        return $stmt->execute();
    }

    public function setStatus(int $electionId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE elections SET status = ? WHERE election_id = ?");
        return $stmt->execute([$status, $electionId]);
    }

    public function getActive(): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM elections WHERE status = 'active' AND start_date <= NOW() AND end_date >= NOW() LIMIT 1");
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getUpcoming(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM elections WHERE start_date > NOW() ORDER BY start_date ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
