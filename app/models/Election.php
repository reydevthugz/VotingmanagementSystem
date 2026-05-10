<?php
namespace App\Models;

class Election
{
    public function all(): array
    {
        $elections = MockStorage::getElections();
        usort($elections, static fn($a, $b) => strcmp($b['start_date'], $a['start_date']));
        return $elections;
    }

    public function create(array $data): void
    {
        $elections = MockStorage::getElections();
        $elections[] = [
            'election_id' => MockStorage::nextId('elections'),
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
        ];
        MockStorage::setElections($elections);
    }

    public function update(int $electionId, array $data): void
    {
        $elections = MockStorage::getElections();
        foreach ($elections as &$election) {
            if ($election['election_id'] === $electionId) {
                $election['title'] = $data['title'];
                $election['start_date'] = $data['start_date'];
                $election['end_date'] = $data['end_date'];
                $election['status'] = $data['status'];
                break;
            }
        }
        MockStorage::setElections($elections);
    }

    public function delete(int $electionId): void
    {
        $elections = array_filter(MockStorage::getElections(), static fn($item) => $item['election_id'] !== $electionId);
        MockStorage::setElections($elections);
    }

    public function deactivateAll(): void
    {
        $elections = array_map(static function ($election) {
            if ($election['status'] === 'active') {
                $election['status'] = 'inactive';
            }
            return $election;
        }, MockStorage::getElections());
        MockStorage::setElections($elections);
    }

    public function setStatus(int $electionId, string $status): void
    {
        $elections = MockStorage::getElections();
        foreach ($elections as &$election) {
            if ($election['election_id'] === $electionId) {
                $election['status'] = $status;
                break;
            }
        }
        MockStorage::setElections($elections);
    }
}
