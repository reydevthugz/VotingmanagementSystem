<?php

namespace App\Models;

use PDO;

class Student extends BaseModel
{
    public function search(array $filters, int $limit, int $offset): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['query'])) {
            $where[] = "(fullname LIKE ? OR email LIKE ? OR course LIKE ? OR section LIKE ?)";
            $query = '%' . $filters['query'] . '%';
            $params = array_merge($params, [$query, $query, $query, $query]);
        }

        if (!empty($filters['course'])) {
            $where[] = "course = ?";
            $params[] = $filters['course'];
        }

        if (!empty($filters['year'])) {
            $where[] = "year = ?";
            $params[] = $filters['year'];
        }

        if (!empty($filters['section'])) {
            $where[] = "section = ?";
            $params[] = $filters['section'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("SELECT * FROM students $whereClause ORDER BY fullname ASC LIMIT ? OFFSET ?");
        $params[] = $limit;
        $params[] = $offset;
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(array $filters): int
    {
        $where = [];
        $params = [];

        if (!empty($filters['query'])) {
            $where[] = "(fullname LIKE ? OR email LIKE ? OR course LIKE ? OR section LIKE ?)";
            $query = '%' . $filters['query'] . '%';
            $params = array_merge($params, [$query, $query, $query, $query]);
        }

        if (!empty($filters['course'])) {
            $where[] = "course = ?";
            $params[] = $filters['course'];
        }

        if (!empty($filters['year'])) {
            $where[] = "year = ?";
            $params[] = $filters['year'];
        }

        if (!empty($filters['section'])) {
            $where[] = "section = ?";
            $params[] = $filters['section'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM students $whereClause");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO students (fullname, email, course, year, section, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['fullname'],
            $data['email'],
            $data['course'],
            $data['year'],
            $data['section'],
            password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $studentId, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE students SET fullname = ?, email = ?, course = ?, year = ?, section = ? WHERE student_id = ?");
        return $stmt->execute([
            $data['fullname'],
            $data['email'],
            $data['course'],
            $data['year'],
            $data['section'],
            $studentId
        ]);
    }

    public function delete(int $studentId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM students WHERE student_id = ?");
        return $stmt->execute([$studentId]);
    }

    public function find(int $studentId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->execute([$studentId]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function existsEmail(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM students WHERE email = ?";
        $params = [$email];

        if ($excludeId !== null) {
            $sql .= " AND student_id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function distinctCourses(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT course FROM students WHERE course != '' ORDER BY course");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function distinctYears(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT year FROM students WHERE year != '' ORDER BY year");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function distinctSections(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT section FROM students WHERE section != '' ORDER BY section");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function updateVotingStatus(int $studentId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE students SET voting_status = ? WHERE student_id = ?");
        return $stmt->execute([$status, $studentId]);
    }
}
