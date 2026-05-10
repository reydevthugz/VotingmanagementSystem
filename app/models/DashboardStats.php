<?php
namespace App\Models;

class DashboardStats extends BaseModel
{
    public function summary(): array
    {
        return [
            'total_voters' => $this->count('students'),
            'total_candidates' => $this->count('candidates'),
            'total_votes_cast' => $this->count('votes'),
            'active_election_status' => $this->activeElectionStatus(),
        ];
    }

    public function votesTrend(int $days = 7): array
    {
        $days = max(1, min($days, 31));
        $sql = "SELECT DATE(voted_at) AS vote_date, COUNT(*) AS total
                FROM votes
                WHERE voted_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(voted_at)
                ORDER BY vote_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll() ?: [];

        $series = [];
        foreach ($rows as $row) {
            $series[] = [
                'date' => $row['vote_date'],
                'total' => (int) $row['total'],
            ];
        }

        return $series;
    }

    private function count(string $table): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM {$table}");
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    private function activeElectionStatus(): string
    {
        $sql = "SELECT status
                FROM elections
                ORDER BY (status = 'active') DESC, start_date DESC
                LIMIT 1";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return $row['status'] ?? 'inactive';
    }
}
