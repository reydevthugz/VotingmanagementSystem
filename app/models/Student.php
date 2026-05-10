<?php
namespace App\Models;

class Student
{
    public function search(array $filters, int $limit, int $offset): array
    {
        $students = $this->applyFilters(MockStorage::getStudents(), $filters);
        usort($students, static fn($a, $b) => strcasecmp($a['fullname'], $b['fullname']));
        return array_slice($students, $offset, $limit);
    }

    public function count(array $filters): int
    {
        return count($this->applyFilters(MockStorage::getStudents(), $filters));
    }

    public function create(array $data): void
    {
        $students = MockStorage::getStudents();
        $students[] = [
            'student_id' => MockStorage::nextId('students'),
            'fullname' => $data['fullname'],
            'course' => $data['course'],
            'year' => $data['year'],
            'section' => $data['section'],
            'email' => $data['email'],
        ];
        MockStorage::setStudents($students);
    }

    public function update(int $studentId, array $data): void
    {
        $students = MockStorage::getStudents();
        foreach ($students as &$student) {
            if ($student['student_id'] === $studentId) {
                $student['fullname'] = $data['fullname'];
                $student['course'] = $data['course'];
                $student['year'] = $data['year'];
                $student['section'] = $data['section'];
                $student['email'] = $data['email'];
                break;
            }
        }
        MockStorage::setStudents($students);
    }

    public function delete(int $studentId): void
    {
        $students = array_filter(MockStorage::getStudents(), static fn($student) => $student['student_id'] !== $studentId);
        MockStorage::setStudents($students);
    }

    public function find(int $studentId): ?array
    {
        foreach (MockStorage::getStudents() as $student) {
            if ($student['student_id'] === $studentId) {
                return $student;
            }
        }
        return null;
    }

    public function existsEmail(string $email, ?int $excludeId = null): bool
    {
        foreach (MockStorage::getStudents() as $student) {
            if (strcasecmp($student['email'], $email) === 0 && $student['student_id'] !== $excludeId) {
                return true;
            }
        }
        return false;
    }

    public function distinctCourses(): array
    {
        return $this->distinctValues(MockStorage::getStudents(), 'course');
    }

    public function distinctYears(): array
    {
        return $this->distinctValues(MockStorage::getStudents(), 'year');
    }

    public function distinctSections(): array
    {
        return $this->distinctValues(MockStorage::getStudents(), 'section');
    }

    private function applyFilters(array $students, array $filters): array
    {
        return array_values(array_filter($students, static function ($student) use ($filters) {
            if (!empty($filters['query'])) {
                $query = mb_strtolower($filters['query']);
                $text = mb_strtolower($student['fullname'] . ' ' . $student['email'] . ' ' . $student['course'] . ' ' . $student['section']);
                if (!str_contains($text, $query)) {
                    return false;
                }
            }

            if (!empty($filters['course']) && strcasecmp($student['course'], $filters['course']) !== 0) {
                return false;
            }

            if (!empty($filters['year']) && strcasecmp($student['year'], $filters['year']) !== 0) {
                return false;
            }

            if (!empty($filters['section']) && strcasecmp($student['section'], $filters['section']) !== 0) {
                return false;
            }

            return true;
        }));
    }

    private function distinctValues(array $items, string $key): array
    {
        $values = array_unique(array_filter(array_map(static fn($item) => $item[$key] ?? '', $items), static fn($value) => $value !== ''));
        sort($values, SORT_NATURAL | SORT_FLAG_CASE);
        return array_values($values);
    }
}
