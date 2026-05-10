<?php

namespace App\Models;

class Vote extends BaseModel
{
    public function activeElection(): ?array
    {
        $stmt = $this->db->prepare("SELECT election_id, title FROM elections WHERE status = 'active' AND start_date <= NOW() AND end_date >= NOW() ORDER BY start_date DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function ballotByPosition(): array
    {
        $stmt = $this->db->query("
            SELECT
                p.position_id,
                p.position_name,
                p.max_votes,
                c.candidate_id,
                c.fullname,
                c.photo,
                c.motto,
                pl.party_name
            FROM positions p
            LEFT JOIN candidates c ON p.position_id = c.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            ORDER BY p.position_name ASC, c.fullname ASC
        ");

        $results = $stmt->fetchAll();
        $grouped = [];

        foreach ($results as $row) {
            $positionId = $row['position_id'];

            if (!isset($grouped[$positionId])) {
                $grouped[$positionId] = [
                    'position_id' => $row['position_id'],
                    'position_name' => $row['position_name'],
                    'max_votes' => $row['max_votes'],
                    'candidates' => []
                ];
            }

            if ($row['candidate_id']) {
                $grouped[$positionId]['candidates'][] = [
                    'candidate_id' => $row['candidate_id'],
                    'fullname' => $row['fullname'],
                    'photo' => $row['photo'] ?: '',
                    'motto' => $row['motto'] ?: '',
                    'party_name' => $row['party_name'] ?: 'Independent'
                ];
            }
        }

        return array_values($grouped);
    }

    public function studentVotes(int $studentId, int $electionId): array
    {
        $stmt = $this->db->prepare("SELECT position_id, candidate_id FROM votes WHERE student_id = ? AND election_id = ?");
        $stmt->execute([$studentId, $electionId]);
        $votes = $stmt->fetchAll();

        $result = [];
        foreach ($votes as $vote) {
            $result[$vote['position_id']] = $vote['candidate_id'];
        }
        return $result;
    }

    public function hasVotedInElection(int $studentId, int $electionId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM votes WHERE student_id = ? AND election_id = ?");
        $stmt->execute([$studentId, $electionId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function candidateById(int $candidateId): ?array
    {
        $stmt = $this->db->prepare("SELECT candidate_id, position_id FROM candidates WHERE candidate_id = ?");
        $stmt->execute([$candidateId]);
        return $stmt->fetch() ?: null;
    }

    public function confirmationDetails(int $studentId, int $electionId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                p.position_name,
                c.fullname as candidate_name,
                pl.party_name
            FROM votes v
            JOIN candidates c ON v.candidate_id = c.candidate_id
            JOIN positions p ON v.position_id = p.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            WHERE v.student_id = ? AND v.election_id = ?
            ORDER BY p.position_name ASC
        ");
        $stmt->execute([$studentId, $electionId]);
        return $stmt->fetchAll();
    }

    public function createVote(int $studentId, int $candidateId, int $positionId, int $electionId): int
    {
        $stmt = $this->db->prepare("INSERT INTO votes (student_id, candidate_id, election_id) VALUES (?, ?, ?)");
        $stmt->execute([$studentId, $candidateId, $electionId]);
        return (int) $this->db->lastInsertId();
    }

    public function getVoteCounts(int $electionId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                c.candidate_id,
                c.fullname,
                p.position_name,
                pl.party_name,
                COUNT(v.vote_id) as vote_count
            FROM candidates c
            JOIN positions p ON c.position_id = p.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            LEFT JOIN votes v ON c.candidate_id = v.candidate_id AND v.election_id = ?
            GROUP BY c.candidate_id, c.fullname, p.position_name, pl.party_name
            ORDER BY p.position_name ASC, vote_count DESC
        ");
        $stmt->execute([$electionId]);
        return $stmt->fetchAll();
    }
}
