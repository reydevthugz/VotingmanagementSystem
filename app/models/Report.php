<?php
namespace App\Models;

class Report
{
    public function elections(): array
    {
        $elections = MockStorage::getElections();
        usort($elections, static fn($a, $b) => strcmp($b['start_date'], $a['start_date']));
        return array_map(static fn($election) => [
            'election_id' => $election['election_id'],
            'title' => $election['title'],
            'status' => $election['status'],
        ], $elections);
    }

    public function electionById(int $electionId): ?array
    {
        foreach (MockStorage::getElections() as $election) {
            if ($election['election_id'] === $electionId) {
                return $election;
            }
        }
        return null;
    }

    public function totalVotes(int $electionId): int
    {
        return count(array_filter(MockStorage::getVotes(), static fn($vote) => $vote['election_id'] === $electionId));
    }

    public function totalCandidates(int $electionId): int
    {
        return count(MockStorage::getCandidates());
    }

    public function totalPositions(int $electionId): int
    {
        return count(MockStorage::getPositions());
    }

    public function candidateResults(int $electionId): array
    {
        $positions = MockStorage::getPositions();
        $candidates = MockStorage::getCandidates();
        $parties = MockStorage::getPartyLists();
        $votes = MockStorage::getVotes();

        $results = [];
        foreach ($positions as $position) {
            foreach ($candidates as $candidate) {
                if ($candidate['position_id'] !== $position['position_id']) {
                    continue;
                }

                $votesCount = 0;
                foreach ($votes as $vote) {
                    if ($vote['election_id'] === $electionId && $vote['candidate_id'] === $candidate['candidate_id']) {
                        $votesCount++;
                    }
                }

                $results[] = [
                    'position_id' => $position['position_id'],
                    'position_name' => $position['position_name'],
                    'candidate_id' => $candidate['candidate_id'],
                    'candidate_name' => $candidate['fullname'],
                    'photo' => $candidate['photo'] ?? '',
                    'motto' => $candidate['motto'] ?? '',
                    'party_name' => $parties[array_search($candidate['party_id'], array_column($parties, 'party_id'))]['party_name'] ?? 'Independent',
                    'votes' => $votesCount,
                ];
            }
        }

        usort($results, static fn($a, $b) => $a['position_name'] <=> $b['position_name'] ?: $b['votes'] <=> $a['votes'] ?: strcmp($a['candidate_name'], $b['candidate_name']));
        return $results;
    }
}
