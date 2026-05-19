<?php

namespace App\Models;

class DashboardStats extends BaseModel
{
    public function summary(): array
    {
        return [
            'total_voters' => $this->countStudents(),
            'total_candidates' => $this->countCandidates(),
            'total_votes_cast' => $this->countVotes(),
            'active_election_status' => $this->activeElectionStatus(),
        ];
    }

    public function votesTrend(int $days = 7): array
    {
        $days = max(1, min($days, 31));

        $stmt = $this->db->prepare("
            SELECT DATE(timestamp) as date, COUNT(*) as total
            FROM votes
            WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(timestamp)
            ORDER BY date ASC
        ");
        $stmt->execute([$days]);
        $results = $stmt->fetchAll();

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime(sprintf('-%d days', $i)));
            $series[$date] = 0;
        }

        foreach ($results as $row) {
            $series[$row['date']] = (int) $row['total'];
        }

        return array_map(static fn($date, $total) => ['date' => $date, 'total' => $total], array_keys($series), $series);
    }

    private function countStudents(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM students");
        return (int) $stmt->fetchColumn();
    }

    private function countCandidates(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM candidates");
        return (int) $stmt->fetchColumn();
    }

    private function countVotes(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM votes");
        return (int) $stmt->fetchColumn();
    }

    private function activeElectionStatus(): string
    {
<<<<<<< HEAD
        $activeElection = (new Election())->getActiveElection();
        return $activeElection ? (string) $activeElection['status'] : 'inactive';
=======
        $stmt = $this->db->prepare("SELECT status FROM elections WHERE status = 'active' AND start_date <= NOW() AND end_date >= NOW() ORDER BY start_date DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['status'] : 'inactive';
>>>>>>> ab7ee4836c683c2baa5bb345d3929ebce16bf58f
    }
}
