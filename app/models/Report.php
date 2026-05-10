<?php

namespace App\Models;

class Report extends BaseModel
{
    public function elections(): array
    {
        $stmt = $this->db->query("SELECT election_id, title, status FROM elections ORDER BY start_date DESC");
        return $stmt->fetchAll();
    }

    public function electionById(int $electionId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM elections WHERE election_id = ?");
        $stmt->execute([$electionId]);
        return $stmt->fetch() ?: null;
    }

    public function totalVotes(int $electionId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM votes WHERE election_id = ?");
        $stmt->execute([$electionId]);
        return (int) $stmt->fetchColumn();
    }

    public function totalCandidates(int $electionId): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM candidates");
        return (int) $stmt->fetchColumn();
    }

    public function totalPositions(int $electionId): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM positions");
        return (int) $stmt->fetchColumn();
    }

    public function candidateResults(int $electionId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                p.position_id,
                p.position_name,
                c.candidate_id,
                c.fullname as candidate_name,
                c.photo,
                c.motto,
                pl.party_name,
                COUNT(v.vote_id) as votes
            FROM candidates c
            JOIN positions p ON c.position_id = p.position_id
            LEFT JOIN party_lists pl ON c.party_id = pl.party_id
            LEFT JOIN votes v ON c.candidate_id = v.candidate_id AND v.election_id = ?
            GROUP BY p.position_id, p.position_name, c.candidate_id, c.fullname, c.photo, c.motto, pl.party_name
            ORDER BY p.position_name ASC, votes DESC, c.fullname ASC
        ");
        $stmt->execute([$electionId]);
        return $stmt->fetchAll();
    }

    public function voterTurnout(int $electionId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(DISTINCT v.student_id) as voted_count,
                (SELECT COUNT(*) FROM students) as total_voters,
                ROUND((COUNT(DISTINCT v.student_id) / (SELECT COUNT(*) FROM students)) * 100, 2) as turnout_percentage
            FROM votes v
            WHERE v.election_id = ?
        ");
        $stmt->execute([$electionId]);
        return $stmt->fetch();
    }
}
