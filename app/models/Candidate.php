<?php
namespace App\Models;

class Candidate extends BaseModel
{
    public function all(): array
    {
        $sql = 'SELECT c.candidate_id, c.fullname, c.photo, c.motto, c.position_id, c.party_id,
                       p.position_name, l.party_name
                FROM candidates AS c
                LEFT JOIN positions AS p ON c.position_id = p.position_id
                LEFT JOIN partylists AS l ON c.party_id = l.party_id
                ORDER BY c.created_at DESC';

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    public function find(int $candidateId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM candidates WHERE candidate_id = :candidate_id');
        $stmt->execute(['candidate_id' => $candidateId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): void
    {
        $sql = 'INSERT INTO candidates (fullname, photo, motto, position_id, party_id)
                VALUES (:fullname, :photo, :motto, :position_id, :party_id)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fullname' => $data['fullname'],
            'photo' => $data['photo'],
            'motto' => $data['motto'],
            'position_id' => $data['position_id'],
            'party_id' => $data['party_id'],
        ]);
    }

    public function update(int $candidateId, array $data): void
    {
        $sql = 'UPDATE candidates
                SET fullname = :fullname,
                    photo = :photo,
                    motto = :motto,
                    position_id = :position_id,
                    party_id = :party_id,
                    updated_at = NOW()
                WHERE candidate_id = :candidate_id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fullname' => $data['fullname'],
            'photo' => $data['photo'],
            'motto' => $data['motto'],
            'position_id' => $data['position_id'],
            'party_id' => $data['party_id'],
            'candidate_id' => $candidateId,
        ]);
    }

    public function delete(int $candidateId): void
    {
        $stmt = $this->db->prepare('DELETE FROM candidates WHERE candidate_id = :candidate_id');
        $stmt->execute(['candidate_id' => $candidateId]);
    }
}
