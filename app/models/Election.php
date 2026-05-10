<?php
namespace App\Models;

class Election extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT election_id, title, start_date, end_date, status FROM elections ORDER BY start_date DESC');
        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): void
    {
        $sql = 'INSERT INTO elections (title, start_date, end_date, status) VALUES (:title, :start_date, :end_date, :status)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
        ]);
    }

    public function update(int $electionId, array $data): void
    {
        $sql = 'UPDATE elections SET title = :title, start_date = :start_date, end_date = :end_date, status = :status, updated_at = NOW() WHERE election_id = :election_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'election_id' => $electionId,
        ]);
    }

    public function delete(int $electionId): void
    {
        $stmt = $this->db->prepare('DELETE FROM elections WHERE election_id = :election_id');
        $stmt->execute(['election_id' => $electionId]);
    }

    public function deactivateAll(): void
    {
        $this->db->exec("UPDATE elections SET status = 'inactive', updated_at = NOW() WHERE status = 'active'");
    }

    public function setStatus(int $electionId, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE elections SET status = :status, updated_at = NOW() WHERE election_id = :election_id');
        $stmt->execute([
            'status' => $status,
            'election_id' => $electionId,
        ]);
    }
}
