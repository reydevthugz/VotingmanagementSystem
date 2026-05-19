<?php
namespace App\Controllers;

use App\Models\Election;
use Throwable;

class ElectionController extends BaseController
{
    public function index(): void
    {
        $electionModel = new Election();
        $this->render('election/index', [
            'pageTitle' => 'Election Management',
            'elections' => $electionModel->all(),
            'errors' => flash('_errors') ?? [],
            'old' => flash('_old') ?? [],
            'notice' => flash(),
        ]);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/elections', $errors, $_POST);
        }

        try {
            $electionModel = new Election();
            if ($data['status'] === 'active') {
                $electionModel->deactivateAll();
            }
            $electionModel->create($data);
            $this->recordActivity('election_create', ['title' => $data['title'], 'status' => $data['status']]);
            $this->flash('success', 'Election created successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to create election.');
        }

        $this->redirect('/admin/elections');
    }

    public function update(): void
    {
        $electionId = (int) ($_POST['election_id'] ?? 0);
        if ($electionId <= 0) {
            $this->flash('danger', 'Invalid election record.');
            $this->redirect('/admin/elections');
        }

        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/elections', $errors, $_POST);
        }

        try {
            $electionModel = new Election();
            if ($data['status'] === 'active') {
                $electionModel->deactivateAll();
            }
            $electionModel->update($electionId, $data);
            $this->recordActivity('election_update', ['election_id' => $electionId, 'title' => $data['title'], 'status' => $data['status']]);
            $this->flash('success', 'Election updated successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to update election.');
        }

        $this->redirect('/admin/elections');
    }

    public function delete(): void
    {
        $electionId = (int) ($_POST['election_id'] ?? 0);
        if ($electionId <= 0) {
            $this->flash('danger', 'Invalid election record.');
            $this->redirect('/admin/elections');
        }

        try {
            (new Election())->delete($electionId);
            $this->recordActivity('election_delete', ['election_id' => $electionId]);
            $this->flash('success', 'Election deleted successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to delete election.');
        }

        $this->redirect('/admin/elections');
    }

    public function activate(): void
    {
        $electionId = (int) ($_POST['election_id'] ?? 0);
        if ($electionId <= 0) {
            $this->flash('danger', 'Invalid election record.');
            $this->redirect('/admin/elections');
        }

        try {
            $electionModel = new Election();
            if (!$electionModel->activate($electionId)) {
                $this->flash('danger', 'Election record not found.');
                $this->redirect('/admin/elections');
            }
            $this->recordActivity('election_activate', ['election_id' => $electionId]);
            $this->flash('success', 'Election activated successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to activate election.');
        }

        $this->redirect('/admin/elections');
    }

    public function deactivate(): void
    {
        $electionId = (int) ($_POST['election_id'] ?? 0);
        if ($electionId <= 0) {
            $this->flash('danger', 'Invalid election record.');
            $this->redirect('/admin/elections');
        }

        try {
            (new Election())->setStatus($electionId, 'inactive');
            $this->recordActivity('election_deactivate', ['election_id' => $electionId]);
            $this->flash('success', 'Election deactivated successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to deactivate election.');
        }

        $this->redirect('/admin/elections');
    }

    private function validate(array $input): array
    {
        $title = trim((string) ($input['title'] ?? ''));
        $startDate = (string) ($input['start_date'] ?? '');
        $endDate = (string) ($input['end_date'] ?? '');
        $status = (string) ($input['status'] ?? 'inactive');
        $errors = [];

        if ($title === '') {
            $errors['title'] = 'Election title is required.';
        }

        if ($startDate === '') {
            $errors['start_date'] = 'Start date is required.';
        }

        if ($endDate === '') {
            $errors['end_date'] = 'End date is required.';
        }

        $startTs = $startDate !== '' ? strtotime($startDate) : false;
        $endTs = $endDate !== '' ? strtotime($endDate) : false;

        if ($startDate !== '' && $startTs === false) {
            $errors['start_date'] = 'Start date is invalid.';
        }

        if ($endDate !== '' && $endTs === false) {
            $errors['end_date'] = 'End date is invalid.';
        }

        if ($startTs !== false && $endTs !== false && $endTs < $startTs) {
            $errors['date_range'] = 'End date must be later than start date.';
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = 'inactive';
        }

        return [[
            'title' => $title,
            'start_date' => $startTs ? date('Y-m-d H:i:s', $startTs) : '',
            'end_date' => $endTs ? date('Y-m-d H:i:s', $endTs) : '',
            'status' => $status,
        ], $errors];
    }
}
