<?php
namespace App\Models;

class Position
{
    public function all(): array
    {
        $positions = MockStorage::getPositions();
        usort($positions, static fn($a, $b) => strcasecmp($a['position_name'], $b['position_name']));
        return $positions;
    }

    public function create(array $data): void
    {
        $positions = MockStorage::getPositions();
        $positions[] = [
            'position_id' => MockStorage::nextId('positions'),
            'position_name' => $data['position_name'],
            'max_votes' => $data['max_votes'],
        ];
        MockStorage::setPositions($positions);
    }

    public function update(int $positionId, array $data): void
    {
        $positions = MockStorage::getPositions();
        foreach ($positions as &$position) {
            if ($position['position_id'] === $positionId) {
                $position['position_name'] = $data['position_name'];
                $position['max_votes'] = $data['max_votes'];
                break;
            }
        }
        MockStorage::setPositions($positions);
    }

    public function delete(int $positionId): void
    {
        $positions = array_filter(MockStorage::getPositions(), static fn($item) => $item['position_id'] !== $positionId);
        MockStorage::setPositions($positions);
    }
}
