<?php
namespace App\Models;

class Position extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT position_id, position_name, max_votes FROM positions ORDER BY position_name ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): void
    {
        $sql = 'INSERT INTO positions (position_name, max_votes) VALUES (:position_name, :max_votes)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'position_name' => $data['position_name'],
            'max_votes' => $data['max_votes'],
        ]);
    }

    public function update(int $positionId, array $data): void
    {
        $sql = 'UPDATE positions SET position_name = :position_name, max_votes = :max_votes WHERE position_id = :position_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'position_name' => $data['position_name'],
            'max_votes' => $data['max_votes'],
            'position_id' => $positionId,
        ]);
    }

    public function delete(int $positionId): void
    {
        $stmt = $this->db->prepare('DELETE FROM positions WHERE position_id = :position_id');
        $stmt->execute(['position_id' => $positionId]);
    }
}
