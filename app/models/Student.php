<?php
namespace App\Models;

use PDO;

class Student extends BaseModel
{
    public function search(array $filters, int $limit, int $offset): array
    {
        $sql = 'SELECT student_id, fullname, course, year, section, email FROM students';
        [$where, $params] = $this->buildFilterQuery($filters);

        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        $sql .= ' ORDER BY fullname ASC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function count(array $filters): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM students';
        [$where, $params] = $this->buildFilterQuery($filters);

        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO students (fullname, course, year, section, email)
            VALUES (:fullname, :course, :year, :section, :email)');

        $stmt->execute([
            'fullname' => $data['fullname'],
            'course' => $data['course'],
            'year' => $data['year'],
            'section' => $data['section'],
            'email' => $data['email'],
        ]);
    }

    public function update(int $studentId, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE students
            SET fullname = :fullname,
                course = :course,
                year = :year,
                section = :section,
                email = :email,
                updated_at = NOW()
            WHERE student_id = :student_id');

        $stmt->execute([
            'fullname' => $data['fullname'],
            'course' => $data['course'],
            'year' => $data['year'],
            'section' => $data['section'],
            'email' => $data['email'],
            'student_id' => $studentId,
        ]);
    }

    public function delete(int $studentId): void
    {
        $stmt = $this->db->prepare('DELETE FROM students WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
    }

    public function find(int $studentId): ?array
    {
        $stmt = $this->db->prepare('SELECT student_id, fullname, course, year, section, email FROM students WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function existsEmail(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM students WHERE email = :email';
        if ($excludeId !== null) {
            $sql .= ' AND student_id != :student_id';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('email', $email);
        if ($excludeId !== null) {
            $stmt->bindValue('student_id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    public function distinctCourses(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT course FROM students WHERE course != "" ORDER BY course ASC');
        return array_column($stmt->fetchAll() ?: [], 'course');
    }

    public function distinctYears(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT year FROM students WHERE year != "" ORDER BY year ASC');
        return array_column($stmt->fetchAll() ?: [], 'year');
    }

    public function distinctSections(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT section FROM students WHERE section != "" ORDER BY section ASC');
        return array_column($stmt->fetchAll() ?: [], 'section');
    }

    private function buildFilterQuery(array $filters): array
    {
        $clauses = [];
        $params = [];

        if (!empty($filters['query'])) {
            $clauses[] = '(fullname LIKE :query OR email LIKE :query OR course LIKE :query OR section LIKE :query)';
            $params['query'] = '%' . $filters['query'] . '%';
        }

        if (!empty($filters['course'])) {
            $clauses[] = 'course = :course';
            $params['course'] = $filters['course'];
        }

        if (!empty($filters['year'])) {
            $clauses[] = 'year = :year';
            $params['year'] = $filters['year'];
        }

        if (!empty($filters['section'])) {
            $clauses[] = 'section = :section';
            $params['section'] = $filters['section'];
        }

        return [implode(' AND ', $clauses), $params];
    }
}
