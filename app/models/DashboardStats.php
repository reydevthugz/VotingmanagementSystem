<?php
namespace App\Models;

class DashboardStats
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
        $cutoff = strtotime(sprintf('-%d days', $days));
        $votes = array_filter(MockStorage::getVotes(), static fn($vote) => strtotime($vote['voted_at']) >= $cutoff);

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime(sprintf('-%d days', $i)));
            $series[$date] = 0;
        }

        foreach ($votes as $vote) {
            $date = date('Y-m-d', strtotime($vote['voted_at']));
            if (isset($series[$date])) {
                $series[$date]++;
            }
        }

        return array_map(static fn($date, $total) => ['date' => $date, 'total' => $total], array_keys($series), $series);
    }

    private function countStudents(): int
    {
        return count(MockStorage::getStudents());
    }

    private function countCandidates(): int
    {
        return count(MockStorage::getCandidates());
    }

    private function countVotes(): int
    {
        return count(MockStorage::getVotes());
    }

    private function activeElectionStatus(): string
    {
        $elections = array_filter(MockStorage::getElections(), static fn($election) => $election['status'] === 'active');
        if (empty($elections)) {
            return 'inactive';
        }

        usort($elections, static fn($a, $b) => strcmp($b['start_date'], $a['start_date']));
        return $elections[0]['status'] ?? 'inactive';
    }
}
