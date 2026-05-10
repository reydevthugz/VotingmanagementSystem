<?php
namespace App\Models;

class PartyList extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT party_id, party_name, description FROM partylists ORDER BY party_name ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): void
    {
        $sql = 'INSERT INTO partylists (party_name, description) VALUES (:party_name, :description)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'party_name' => $data['party_name'],
            'description' => $data['description'],
        ]);
    }

    public function update(int $partyId, array $data): void
    {
        $sql = 'UPDATE partylists SET party_name = :party_name, description = :description WHERE party_id = :party_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'party_name' => $data['party_name'],
            'description' => $data['description'],
            'party_id' => $partyId,
        ]);
    }

    public function delete(int $partyId): void
    {
        $stmt = $this->db->prepare('DELETE FROM partylists WHERE party_id = :party_id');
        $stmt->execute(['party_id' => $partyId]);
    }
}
