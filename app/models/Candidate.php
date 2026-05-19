<?php

namespace App\Models;

class Candidate extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, p.position_name, pl.party_name
            FROM candidates c
            LEFT JOIN positions p ON c.position_id = p.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function find(int $candidateId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, p.position_name, pl.party_name
            FROM candidates c
            LEFT JOIN positions p ON c.position_id = p.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            WHERE c.candidate_id = ?
        ");
        $stmt->execute([$candidateId]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO candidates (fullname, photo, motto, position_id, party_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['fullname'],
            $data['photo'] ?? '',
            $data['motto'] ?? '',
            $data['position_id'] ?? null,
            $data['party_id'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $candidateId, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE candidates SET fullname = ?, photo = ?, motto = ?, position_id = ?, party_id = ? WHERE candidate_id = ?");
        return $stmt->execute([
            $data['fullname'],
            $data['photo'] ?? '',
            $data['motto'] ?? '',
            $data['position_id'] ?? null,
            $data['party_id'] ?? null,
            $candidateId
        ]);
    }

    public function delete(int $candidateId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM candidates WHERE candidate_id = ?");
        return $stmt->execute([$candidateId]);
    }

    public function getByPosition(int $positionId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, p.position_name, pl.party_name
            FROM candidates c
            LEFT JOIN positions p ON c.position_id = p.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            WHERE c.position_id = ?
            ORDER BY c.fullname ASC
        ");
        $stmt->execute([$positionId]);
        return $stmt->fetchAll();
    }

    public function getByElection(int $electionId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, p.position_name, pl.party_name, p.max_votes
            FROM candidates c
            LEFT JOIN positions p ON c.position_id = p.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            WHERE c.position_id IS NOT NULL
            ORDER BY p.position_name ASC, c.fullname ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
