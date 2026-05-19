<?php
namespace App\Controllers;

use App\Models\Position;
use Throwable;

class PositionController extends BaseController
{
    public function index(): void
    {
        $positionModel = new Position();
        $this->render('position/index', [
            'pageTitle' => 'Position Management',
            'positions' => $positionModel->all(),
            'errors' => flash('_errors') ?? [],
            'old' => flash('_old') ?? [],
            'notice' => flash(),
        ]);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/positions', $errors, $_POST);
        }

        try {
            (new Position())->create($data);
            $this->recordActivity('position_create', ['position_name' => $data['position_name'], 'max_votes' => $data['max_votes']]);
            $this->flash('success', 'Position created successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to create position.');
        }

        $this->redirect('/admin/positions');
    }

    public function update(): void
    {
        $positionId = (int) ($_POST['position_id'] ?? 0);
        if ($positionId <= 0) {
            $this->flash('danger', 'Invalid position record.');
            $this->redirect('/admin/positions');
        }

        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/positions', $errors, $_POST);
        }

        try {
            (new Position())->update($positionId, $data);
            $this->recordActivity('position_update', ['position_id' => $positionId, 'position_name' => $data['position_name'], 'max_votes' => $data['max_votes']]);
            $this->flash('success', 'Position updated successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to update position.');
        }

        $this->redirect('/admin/positions');
    }

    public function delete(): void
    {
        $positionId = (int) ($_POST['position_id'] ?? 0);
        if ($positionId <= 0) {
            $this->flash('danger', 'Invalid position record.');
            $this->redirect('/admin/positions');
        }

        try {
            (new Position())->delete($positionId);
            $this->recordActivity('position_delete', ['position_id' => $positionId]);
            $this->flash('success', 'Position deleted successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to delete position.');
        }

        $this->redirect('/admin/positions');
    }

    private function validate(array $input): array
    {
        $positionName = trim((string) ($input['position_name'] ?? ''));
        $maxVotesRaw = (string) ($input['max_votes'] ?? '');
        $errors = [];

        if ($positionName === '') {
            $errors['position_name'] = 'Position name is required.';
        }

        if ($maxVotesRaw === '' || filter_var($maxVotesRaw, FILTER_VALIDATE_INT) === false) {
            $errors['max_votes'] = 'Maximum votes must be a whole number.';
        }

        $maxVotes = (int) $maxVotesRaw;
        if ($maxVotes < 1) {
            $errors['max_votes'] = 'Maximum votes must be at least 1.';
        }

        return [[
            'position_name' => $positionName,
            'max_votes' => $maxVotes,
        ], $errors];
    }
}
