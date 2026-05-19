<?php
namespace App\Controllers;

use App\Models\PartyList;
use Throwable;

class PartyListController extends BaseController
{
    public function index(): void
    {
        $partyListModel = new PartyList();
        $this->render('partylist/index', [
            'pageTitle' => 'Party List Management',
            'partylists' => $partyListModel->all(),
            'errors' => flash('_errors') ?? [],
            'old' => flash('_old') ?? [],
            'notice' => flash(),
        ]);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/partylists', $errors, $_POST);
        }

        try {
            (new PartyList())->create($data);
            $this->recordActivity('party_create', ['party_name' => $data['party_name']]);
            $this->flash('success', 'Party list created successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to create party list.');
        }

        $this->redirect('/admin/partylists');
    }

    public function update(): void
    {
        $partyId = (int) ($_POST['party_id'] ?? 0);
        if ($partyId <= 0) {
            $this->flash('danger', 'Invalid party list record.');
            $this->redirect('/admin/partylists');
        }

        [$data, $errors] = $this->validate($_POST);
        if (!empty($errors)) {
            $this->backWithErrors('/admin/partylists', $errors, $_POST);
        }

        try {
            (new PartyList())->update($partyId, $data);
            $this->recordActivity('party_update', ['party_id' => $partyId, 'party_name' => $data['party_name']]);
            $this->flash('success', 'Party list updated successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to update party list.');
        }

        $this->redirect('/admin/partylists');
    }

    public function delete(): void
    {
        $partyId = (int) ($_POST['party_id'] ?? 0);
        if ($partyId <= 0) {
            $this->flash('danger', 'Invalid party list record.');
            $this->redirect('/admin/partylists');
        }

        try {
            (new PartyList())->delete($partyId);
            $this->recordActivity('party_delete', ['party_id' => $partyId]);
            $this->flash('success', 'Party list deleted successfully.');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Failed to delete party list.');
        }

        $this->redirect('/admin/partylists');
    }

    private function validate(array $input): array
    {
        $partyName = trim((string) ($input['party_name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $errors = [];

        if ($partyName === '') {
            $errors['party_name'] = 'Party name is required.';
        }

        if (strlen($partyName) > 120) {
            $errors['party_name'] = 'Party name must not exceed 120 characters.';
        }

        if (strlen($description) > 500) {
            $errors['description'] = 'Description must not exceed 500 characters.';
        }

        return [[
            'party_name' => $partyName,
            'description' => $description,
        ], $errors];
    }
}
