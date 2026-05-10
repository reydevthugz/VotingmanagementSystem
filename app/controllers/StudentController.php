<?php
namespace App\Controllers;

use App\Models\Student;
use Throwable;

class StudentController extends BaseController
{
    public function index(): void
    {
        $filters = [
            'query' => trim((string) ($_GET['search'] ?? '')),
            'course' => trim((string) ($_GET['course'] ?? '')),
            'year' => trim((string) ($_GET['year'] ?? '')),
            'section' => trim((string) ($_GET['section'] ?? '')),
        ];

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $studentModel = new Student();
        $totalStudents = $studentModel->count($filters);
        $totalPages = max(1, (int) ceil($totalStudents / $perPage));
        $students = $studentModel->search($filters, $perPage, $offset);

        $this->render('student/index', [
            'pageTitle' => 'Student Management',
            'students' => $students,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalStudents' => $totalStudents,
            'courses' => $studentModel->distinctCourses(),
            'years' => $studentModel->distinctYears(),
            'sections' => $studentModel->distinctSections(),
            'errors' => flash('_errors') ?? [],
            'old' => flash('_old') ?? [],
            'notice' => flash(),
        ]);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/students', $errors, $_POST);
        }

        try {
            $studentModel = new Student();
            if ($studentModel->existsEmail($data['email'])) {
                $this->backWithErrors('/admin/students', ['email' => 'Email already exists.'], $_POST);
            }

            $studentModel->create($data);
            $this->recordActivity('student_create', ['fullname' => $data['fullname'], 'email' => $data['email']]);
            $this->flash('success', 'Student registered successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to register student.');
        }

        $this->redirect('/admin/students');
    }

    public function update(): void
    {
        $studentId = (int) ($_POST['student_id'] ?? 0);
        if ($studentId <= 0) {
            $this->flash('danger', 'Invalid student record.');
            $this->redirect('/admin/students');
        }

        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/students', $errors, $_POST);
        }

        try {
            $studentModel = new Student();
            if ($studentModel->existsEmail($data['email'], $studentId)) {
                $this->backWithErrors('/admin/students', ['email' => 'Email already exists.'], $_POST);
            }

            $studentModel->update($studentId, $data);
            $this->recordActivity('student_update', ['student_id' => $studentId, 'email' => $data['email']]);
            $this->flash('success', 'Student account updated successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to update student account.');
        }

        $this->redirect('/admin/students');
    }

    public function delete(): void
    {
        $studentId = (int) ($_POST['student_id'] ?? 0);
        if ($studentId <= 0) {
            $this->flash('danger', 'Invalid student record.');
            $this->redirect('/admin/students');
        }

        try {
            (new Student())->delete($studentId);
            $this->recordActivity('student_delete', ['student_id' => $studentId]);
            $this->flash('success', 'Student deleted successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to delete student.');
        }

        $this->redirect('/admin/students');
    }

    public function import(): void
    {
        if (!isset($_FILES['import_file'])) {
            $this->flash('danger', 'Please select a CSV file to import.');
            $this->redirect('/admin/students');
        }

        $file = $_FILES['import_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->flash('danger', 'CSV upload failed. Please try again.');
            $this->redirect('/admin/students');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if ($mime !== 'text/plain' && $mime !== 'text/csv' && $mime !== 'application/vnd.ms-excel' && $mime !== 'text/comma-separated-values') {
            $this->flash('danger', 'Only CSV files are allowed.');
            $this->redirect('/admin/students');
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
            $header = fgetcsv($handle);
            if ($header === false) {
                $errors[] = 'CSV file is empty or invalid.';
            } else {
                $headerMap = array_change_key_case(array_flip($header));
                $requiredColumns = ['fullname', 'course', 'year', 'section', 'email'];

                foreach ($requiredColumns as $column) {
                    if (!isset($headerMap[$column])) {
                        $errors[] = sprintf('CSV missing required column: %s', $column);
                    }
                }

                if (empty($errors)) {
                    $studentModel = new Student();
                    $rowNumber = 1;

                    while (($row = fgetcsv($handle)) !== false) {
                        $rowNumber++;
                        $item = [];
                        foreach ($requiredColumns as $column) {
                            $item[$column] = trim($row[$headerMap[$column]] ?? '');
                        }

                        [$data, $rowErrors] = $this->validate($item);
                        if (!empty($rowErrors)) {
                            $errors[] = sprintf('Row %d: %s', $rowNumber, implode(' ', $rowErrors));
                            continue;
                        }

                        if ($studentModel->existsEmail($data['email'])) {
                            $skipped++;
                            continue;
                        }

                        try {
                            $studentModel->create($data);
                            $imported++;
                        } catch (Throwable $exception) {
                            $errors[] = sprintf('Row %d: failed to insert student.', $rowNumber);
                        }
                    }
                }
            }

            fclose($handle);
        } else {
            $errors[] = 'Unable to open the CSV file.';
        }

        if (!empty($errors)) {
            $this->flash('danger', implode(' ', $errors));
        } else {
            $message = sprintf('Imported %d students. %d duplicates skipped.', $imported, $skipped);
            $this->recordActivity('student_import', ['imported' => $imported, 'skipped' => $skipped]);
            $this->flash('success', $message);
        }

        $this->redirect('/admin/students');
    }

    private function validate(array $input): array
    {
        $fullname = trim((string) ($input['fullname'] ?? ''));
        $course = trim((string) ($input['course'] ?? ''));
        $year = trim((string) ($input['year'] ?? ''));
        $section = trim((string) ($input['section'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $errors = [];

        if ($fullname === '') {
            $errors['fullname'] = 'Full name is required.';
        } elseif (strlen($fullname) > 150) {
            $errors['fullname'] = 'Full name must not exceed 150 characters.';
        }

        if ($course === '') {
            $errors['course'] = 'Course is required.';
        } elseif (strlen($course) > 100) {
            $errors['course'] = 'Course must not exceed 100 characters.';
        }

        if ($year === '') {
            $errors['year'] = 'Year is required.';
        } elseif (strlen($year) > 20) {
            $errors['year'] = 'Year must not exceed 20 characters.';
        }

        if ($section === '') {
            $errors['section'] = 'Section is required.';
        } elseif (strlen($section) > 50) {
            $errors['section'] = 'Section must not exceed 50 characters.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid.';
        } elseif (strlen($email) > 150) {
            $errors['email'] = 'Email must not exceed 150 characters.';
        }

        return [[
            'fullname' => $fullname,
            'course' => $course,
            'year' => $year,
            'section' => $section,
            'email' => $email,
        ], $errors];
    }
}
