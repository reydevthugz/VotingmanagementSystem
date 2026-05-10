<?php
namespace App\Models;

class Candidate
{
    public function all(): array
    {
        $candidates = MockStorage::getCandidates();
        $positions = MockStorage::getPositions();
        $parties = MockStorage::getPartyLists();

        usort($candidates, static fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        return array_map(static function ($candidate) use ($positions, $parties) {
            return [
                'candidate_id' => $candidate['candidate_id'],
                'fullname' => $candidate['fullname'],
                'photo' => $candidate['photo'] ?? '',
                'motto' => $candidate['motto'] ?? '',
                'position_id' => $candidate['position_id'] ?? null,
                'party_id' => $candidate['party_id'] ?? null,
                'position_name' => $positions[array_search($candidate['position_id'], array_column($positions, 'position_id'))]['position_name'] ?? 'Unassigned',
                'party_name' => $parties[array_search($candidate['party_id'], array_column($parties, 'party_id'))]['party_name'] ?? 'Unassigned',
            ];
        }, $candidates);
    }

    public function find(int $candidateId): ?array
    {
        foreach (MockStorage::getCandidates() as $candidate) {
            if ($candidate['candidate_id'] === $candidateId) {
                return $candidate;
            }
        }
        return null;
    }

    public function create(array $data): void
    {
        $candidates = MockStorage::getCandidates();
        $candidates[] = [
            'candidate_id' => MockStorage::nextId('candidates'),
            'fullname' => $data['fullname'],
            'photo' => $data['photo'] ?? '',
            'motto' => $data['motto'] ?? '',
            'position_id' => $data['position_id'] ?? null,
            'party_id' => $data['party_id'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        MockStorage::setCandidates($candidates);
    }

    public function update(int $candidateId, array $data): void
    {
        $candidates = MockStorage::getCandidates();
        foreach ($candidates as &$candidate) {
            if ($candidate['candidate_id'] === $candidateId) {
                $candidate['fullname'] = $data['fullname'];
                $candidate['photo'] = $data['photo'] ?? $candidate['photo'] ?? '';
                $candidate['motto'] = $data['motto'] ?? '';
                $candidate['position_id'] = $data['position_id'] ?? null;
                $candidate['party_id'] = $data['party_id'] ?? null;
                break;
            }
        }
        MockStorage::setCandidates($candidates);
    }

    public function delete(int $candidateId): void
    {
        $candidates = array_filter(MockStorage::getCandidates(), static fn($item) => $item['candidate_id'] !== $candidateId);
        MockStorage::setCandidates($candidates);
    }
}
