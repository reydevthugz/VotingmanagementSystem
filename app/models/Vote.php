<?php
namespace App\Models;

class Vote
{
    public function activeElection(): ?array
    {
        $elections = array_filter(MockStorage::getElections(), static fn($election) => $election['status'] === 'active');
        if (empty($elections)) {
            return null;
        }

        usort($elections, static fn($a, $b) => strcmp($b['start_date'], $a['start_date']));
        return [
            'election_id' => $elections[0]['election_id'],
            'title' => $elections[0]['title'],
        ];
    }

    public function ballotByPosition(): array
    {
        $positions = MockStorage::getPositions();
        $candidates = MockStorage::getCandidates();
        $parties = MockStorage::getPartyLists();

        usort($positions, static fn($a, $b) => strcasecmp($a['position_name'], $b['position_name']));

        $grouped = [];
        foreach ($positions as $position) {
            $grouped[$position['position_id']] = [
                'position_id' => $position['position_id'],
                'position_name' => $position['position_name'],
                'max_votes' => $position['max_votes'],
                'candidates' => [],
            ];
        }

        foreach ($candidates as $candidate) {
            $positionId = $candidate['position_id'];
            if (!isset($grouped[$positionId])) {
                continue;
            }

            $grouped[$positionId]['candidates'][] = [
                'candidate_id' => $candidate['candidate_id'],
                'fullname' => $candidate['fullname'],
                'photo' => $candidate['photo'] ?? '',
                'motto' => $candidate['motto'] ?? '',
                'party_name' => $parties[array_search($candidate['party_id'], array_column($parties, 'party_id'))]['party_name'] ?? 'Independent',
            ];
        }

        foreach ($grouped as &$position) {
            usort($position['candidates'], static fn($a, $b) => strcasecmp($a['fullname'], $b['fullname']));
        }

        return array_values($grouped);
    }

    public function studentVotes(int $studentId, int $electionId): array
    {
        $votes = array_filter(MockStorage::getVotes(), static fn($vote) => $vote['student_id'] === $studentId && $vote['election_id'] === $electionId);
        $result = [];
        foreach ($votes as $vote) {
            $result[$vote['position_id']] = $vote['candidate_id'];
        }
        return $result;
    }

    public function hasVotedInElection(int $studentId, int $electionId): bool
    {
        foreach (MockStorage::getVotes() as $vote) {
            if ($vote['student_id'] === $studentId && $vote['election_id'] === $electionId) {
                return true;
            }
        }
        return false;
    }

    public function candidateById(int $candidateId): ?array
    {
        foreach (MockStorage::getCandidates() as $candidate) {
            if ($candidate['candidate_id'] === $candidateId) {
                return [
                    'candidate_id' => $candidate['candidate_id'],
                    'position_id' => $candidate['position_id'],
                ];
            }
        }
        return null;
    }

    public function confirmationDetails(int $studentId, int $electionId): array
    {
        $votes = array_filter(MockStorage::getVotes(), static fn($vote) => $vote['student_id'] === $studentId && $vote['election_id'] === $electionId);
        $candidates = MockStorage::getCandidates();
        $positions = MockStorage::getPositions();
        $parties = MockStorage::getPartyLists();

        $details = [];
        foreach ($votes as $vote) {
            $candidate = null;
            foreach ($candidates as $item) {
                if ($item['candidate_id'] === $vote['candidate_id']) {
                    $candidate = $item;
                    break;
                }
            }

            if ($candidate === null) {
                continue;
            }

            $position = array_values(array_filter($positions, static fn($item) => $item['position_id'] === $vote['position_id']));
            $details[] = [
                'position_name' => $position[0]['position_name'] ?? 'Unknown Position',
                'candidate_name' => $candidate['fullname'],
                'party_name' => $parties[array_search($candidate['party_id'], array_column($parties, 'party_id'))]['party_name'] ?? 'Independent',
            ];
        }

        usort($details, static fn($a, $b) => strcasecmp($a['position_name'], $b['position_name']));
        return $details;
    }

    public function createVote(int $studentId, int $candidateId, int $positionId, int $electionId): void
    {
        $votes = MockStorage::getVotes();
        $votes[] = [
            'vote_id' => MockStorage::nextId('votes'),
            'student_id' => $studentId,
            'candidate_id' => $candidateId,
            'position_id' => $positionId,
            'election_id' => $electionId,
            'voted_at' => date('Y-m-d H:i:s'),
        ];
        MockStorage::setVotes($votes);
    }
}
