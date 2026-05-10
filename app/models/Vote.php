<?php
namespace App\Models;

use PDO;

class Vote extends BaseModel
{
    public function activeElection(): ?array
    {
        $stmt = $this->db->prepare('SELECT election_id, title FROM elections WHERE status = :status ORDER BY start_date DESC LIMIT 1');
        $stmt->execute(['status' => 'active']);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function ballotByPosition(): array
    {
        $sql = 'SELECT p.position_id, p.position_name, p.max_votes,
                       c.candidate_id, c.fullname AS candidate_name, c.photo, c.motto,
                       l.party_name
                FROM positions AS p
                LEFT JOIN candidates AS c ON c.position_id = p.position_id
                LEFT JOIN partylists AS l ON c.party_id = l.party_id
                ORDER BY p.position_name ASC, c.fullname ASC';

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll() ?: [];
        $positions = [];

        foreach ($rows as $row) {
            $positionId = (int) $row['position_id'];
            if (!isset($positions[$positionId])) {
                $positions[$positionId] = [
                    'position_id' => $positionId,
                    'position_name' => $row['position_name'],
                    'max_votes' => (int) $row['max_votes'],
                    'candidates' => [],
                ];
            }

            if ($row['candidate_id'] !== null) {
                $positions[$positionId]['candidates'][] = [
                    'candidate_id' => (int) $row['candidate_id'],
                    'fullname' => $row['candidate_name'],
                    'photo' => $row['photo'] ?? '',
                    'motto' => $row['motto'] ?? '',
                    'party_name' => $row['party_name'] ?? 'Independent',
                ];
            }
        }

        return array_values($positions);
    }

    public function studentVotes(int $studentId, int $electionId): array
    {
        $stmt = $this->db->prepare('SELECT position_id, candidate_id FROM votes WHERE student_id = :student_id AND election_id = :election_id');
        $stmt->execute([
            'student_id' => $studentId,
            'election_id' => $electionId,
        ]);
        $rows = $stmt->fetchAll() ?: [];

        $votes = [];
        foreach ($rows as $row) {
            $votes[(int) $row['position_id']] = (int) $row['candidate_id'];
        }

        return $votes;
    }

    public function hasVotedInElection(int $studentId, int $electionId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM votes WHERE student_id = :student_id AND election_id = :election_id LIMIT 1');
        $stmt->execute([
            'student_id' => $studentId,
            'election_id' => $electionId,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    public function candidateById(int $candidateId): ?array
    {
        $stmt = $this->db->prepare('SELECT candidate_id, position_id FROM candidates WHERE candidate_id = :candidate_id');
        $stmt->execute(['candidate_id' => $candidateId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function confirmationDetails(int $studentId, int $electionId): array
    {
        $sql = 'SELECT p.position_name, c.fullname AS candidate_name, l.party_name
                FROM votes AS v
                JOIN candidates AS c ON v.candidate_id = c.candidate_id
                JOIN positions AS p ON v.position_id = p.position_id
                LEFT JOIN partylists AS l ON c.party_id = l.party_id
                WHERE v.student_id = :student_id AND v.election_id = :election_id
                ORDER BY p.position_name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'election_id' => $electionId,
        ]);

        return $stmt->fetchAll() ?: [];
    }

    public function createVote(int $studentId, int $candidateId, int $positionId, int $electionId): void
    {
        $stmt = $this->db->prepare('INSERT INTO votes (student_id, candidate_id, position_id, election_id, voted_at) VALUES (:student_id, :candidate_id, :position_id, :election_id, NOW())');
        $stmt->execute([
            'student_id' => $studentId,
            'candidate_id' => $candidateId,
            'position_id' => $positionId,
            'election_id' => $electionId,
        ]);
    }
}
