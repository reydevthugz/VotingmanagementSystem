<?php
namespace App\Controllers;

use App\Models\Candidate;
use App\Models\PartyList;
use App\Models\Position;
use Throwable;

class CandidateController extends BaseController
{
    public function index(): void
    {
        $candidateModel = new Candidate();
        $positionModel = new Position();
        $partyListModel = new PartyList();

        $this->render('candidate/index', [
            'pageTitle' => 'Candidate Management',
            'candidates' => $candidateModel->all(),
            'positions' => $positionModel->all(),
            'partylists' => $partyListModel->all(),
            'errors' => flash('_errors') ?? [],
            'old' => flash('_old') ?? [],
            'notice' => flash(),
        ]);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST, $_FILES);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/candidates', $errors, $_POST);
        }

        try {
            (new Candidate())->create($data);
            $this->recordActivity('candidate_create', ['fullname' => $data['fullname'], 'position_id' => $data['position_id'], 'party_id' => $data['party_id']]);
            $this->flash('success', 'Candidate registered successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to register candidate.');
        }

        $this->redirect('/admin/candidates');
    }

    public function update(): void
    {
        $candidateId = (int) ($_POST['candidate_id'] ?? 0);
        if ($candidateId <= 0) {
            $this->flash('danger', 'Invalid candidate record.');
            $this->redirect('/admin/candidates');
        }

        $existingPhoto = trim((string) ($_POST['existing_photo'] ?? ''));
        [$data, $errors] = $this->validate($_POST, $_FILES, true);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/candidates', $errors, $_POST);
        }

        try {
            $candidateModel = new Candidate();
            $candidateModel->update($candidateId, $data);

            if ($data['photo'] !== $existingPhoto && $existingPhoto !== '') {
                $this->deleteUploadedPhoto($existingPhoto);
            }

            $this->recordActivity('candidate_update', ['candidate_id' => $candidateId, 'fullname' => $data['fullname']]);
            $this->flash('success', 'Candidate profile updated successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to update candidate profile.');
        }

        $this->redirect('/admin/candidates');
    }

    public function delete(): void
    {
        $candidateId = (int) ($_POST['candidate_id'] ?? 0);
        if ($candidateId <= 0) {
            $this->flash('danger', 'Invalid candidate record.');
            $this->redirect('/admin/candidates');
        }

        try {
            $candidateModel = new Candidate();
            $candidate = $candidateModel->find($candidateId);
            if ($candidate && !empty($candidate['photo'])) {
                $this->deleteUploadedPhoto($candidate['photo']);
            }
            $candidateModel->delete($candidateId);
            $this->recordActivity('candidate_delete', ['candidate_id' => $candidateId, 'fullname' => $candidate['fullname'] ?? null]);
            $this->flash('success', 'Candidate deleted successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to delete candidate.');
        }

        $this->redirect('/admin/candidates');
    }

    private function validate(array $input, array $files = [], bool $isUpdate = false): array
    {
        $fullname = trim((string) ($input['fullname'] ?? ''));
        $motto = trim((string) ($input['motto'] ?? ''));
        $positionId = trim((string) ($input['position_id'] ?? ''));
        $partyId = trim((string) ($input['party_id'] ?? ''));
        $existingPhoto = trim((string) ($input['existing_photo'] ?? ''));
        $errors = [];

        if ($fullname === '') {
            $errors['fullname'] = 'Full name is required.';
        } elseif (strlen($fullname) > 150) {
            $errors['fullname'] = 'Full name must not exceed 150 characters.';
        }

        if ($motto !== '' && strlen($motto) > 255) {
            $errors['motto'] = 'Motto must not exceed 255 characters.';
        }

        if ($positionId !== '' && filter_var($positionId, FILTER_VALIDATE_INT) === false) {
            $errors['position_id'] = 'Invalid position selected.';
        }

        if ($partyId !== '' && filter_var($partyId, FILTER_VALIDATE_INT) === false) {
            $errors['party_id'] = 'Invalid party list selected.';
        }

        $photoPath = $existingPhoto;
        if (!$isUpdate || ($files['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            [$uploadedPath, $photoError] = $this->uploadPhoto($files['photo'] ?? null);
            if ($photoError !== null) {
                $errors['photo'] = $photoError;
            } else {
                $photoPath = $uploadedPath;
            }
        }

        if (!$isUpdate && $photoPath === '') {
            $errors['photo'] = 'Candidate photo is required.';
        }

        return [[
            'fullname' => $fullname,
            'photo' => $photoPath !== '' ? $photoPath : null,
            'motto' => $motto,
            'position_id' => $positionId !== '' ? (int) $positionId : null,
            'party_id' => $partyId !== '' ? (int) $partyId : null,
        ], $errors];
    }

    private function uploadPhoto(?array $file): array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['', null];
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['', 'Invalid photo upload.'];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['', 'Upload failed. Please try again.'];
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return ['', 'Photo must be 2MB or smaller.'];
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            return ['', 'Allowed photo formats are JPG, PNG, and GIF.'];
        }

        $directory = base_path('public/assets/uploads/candidates');
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return ['', 'Unable to create photo upload directory.'];
        }

        $fileName = uniqid('candidate_', true) . '.' . $allowed[$mime];
        $destination = $directory . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['', 'Failed to save the uploaded photo.'];
        }

        return ['assets/uploads/candidates/' . $fileName, null];
    }

    private function deleteUploadedPhoto(string $path): void
    {
        $publicPath = base_path('public/' . ltrim($path, '/'));
        if (is_file($publicPath)) {
            @unlink($publicPath);
        }
    }
}
