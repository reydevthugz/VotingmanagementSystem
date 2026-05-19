<?php

namespace App\Models;

class Election extends BaseModel
{
    private function normalizeStatus(string $status): string
    {
        $normalized = strtolower(trim($status));
        return in_array($normalized, ['active', 'inactive'], true) ? $normalized : 'inactive';
    }

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
            $this->normalizeStatus((string) ($data['status'] ?? 'inactive'))
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
            $this->normalizeStatus((string) ($data['status'] ?? 'inactive')),
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
        $stmt = $this->db->prepare("UPDATE elections SET status = 'inactive' WHERE LOWER(TRIM(status)) = 'active'");
        return $stmt->execute();
    }

    public function setStatus(int $electionId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE elections SET status = ? WHERE election_id = ?");
        return $stmt->execute([$this->normalizeStatus($status), $electionId]);
    }

    public function activate(int $electionId): bool
    {
        $election = $this->find($electionId);
        if (!$election) {
            return false;
        }

        $today = date('Y-m-d');
        $startDate = (string) $election['start_date'];
        $endDate = (string) $election['end_date'];

        // When admin activates, ensure today falls within the voting window.
        if (date('Y-m-d', strtotime($startDate)) > $today) {
            $startDate = $today . ' 00:00:00';
        }
        if (date('Y-m-d', strtotime($endDate)) < $today) {
            $endDate = $today . ' 23:59:59';
        }

        $this->deactivateAll();

        $stmt = $this->db->prepare(
            "UPDATE elections SET status = 'active', start_date = ?, end_date = ? WHERE election_id = ?"
        );

        return $stmt->execute([$startDate, $endDate, $electionId]);
    }

    public function getActiveElection(): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM elections
            WHERE LOWER(TRIM(status)) = 'active'
              AND DATE(start_date) <= CURDATE()
              AND DATE(end_date) >= CURDATE()
            ORDER BY start_date DESC
            LIMIT 1
        ");
        $stmt->execute();
        $election = $stmt->fetch() ?: null;

        if ($election) {
            $election['status'] = $this->normalizeStatus((string) $election['status']);
        }

        return $election;
    }

    public function getActive(): ?array
    {
        return $this->getActiveElection();
    }

    public function getUpcoming(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM elections WHERE start_date > NOW() ORDER BY start_date ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
