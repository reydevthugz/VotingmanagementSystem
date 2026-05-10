<?php
namespace App\Models;

use PDO;

class Report extends BaseModel
{
    public function elections(): array
    {
        $stmt = $this->db->query('SELECT election_id, title, status FROM elections ORDER BY start_date DESC');
        return $stmt->fetchAll() ?: [];
    }

    public function electionById(int $electionId): ?array
    {
        $stmt = $this->db->prepare('SELECT election_id, title, status FROM elections WHERE election_id = :election_id');
        $stmt->execute(['election_id' => $electionId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function totalVotes(int $electionId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM votes WHERE election_id = :election_id');
        $stmt->execute(['election_id' => $electionId]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function totalCandidates(int $electionId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(DISTINCT candidate_id) AS total FROM candidates');
        $stmt->execute();
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function totalPositions(int $electionId): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM positions');
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function candidateResults(int $electionId): array
    {
        $sql = 'SELECT p.position_id,
                       p.position_name,
                       c.candidate_id,
                       c.fullname AS candidate_name,
                       c.photo,
                       c.motto,
                       l.party_name,
                       COUNT(v.vote_id) AS votes
                FROM positions AS p
                LEFT JOIN candidates AS c ON c.position_id = p.position_id
                LEFT JOIN partylists AS l ON c.party_id = l.party_id
                LEFT JOIN votes AS v ON v.candidate_id = c.candidate_id AND v.election_id = :election_id
                GROUP BY p.position_id, p.position_name, c.candidate_id, c.fullname, c.photo, c.motto, l.party_name
                ORDER BY p.position_name ASC, votes DESC, c.fullname ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['election_id' => $electionId]);
        return $stmt->fetchAll() ?: [];
    }
}
