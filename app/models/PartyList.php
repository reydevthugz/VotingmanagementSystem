<?php
namespace App\Models;

class PartyList
{
    public function all(): array
    {
        $lists = MockStorage::getPartyLists();
        usort($lists, static fn($a, $b) => strcasecmp($a['party_name'], $b['party_name']));
        return $lists;
    }

    public function create(array $data): void
    {
        $parties = MockStorage::getPartyLists();
        $parties[] = [
            'party_id' => MockStorage::nextId('partylists'),
            'party_name' => $data['party_name'],
            'description' => $data['description'],
        ];
        MockStorage::setPartyLists($parties);
    }

    public function update(int $partyId, array $data): void
    {
        $parties = MockStorage::getPartyLists();
        foreach ($parties as &$party) {
            if ($party['party_id'] === $partyId) {
                $party['party_name'] = $data['party_name'];
                $party['description'] = $data['description'];
                break;
            }
        }
        MockStorage::setPartyLists($parties);
    }

    public function delete(int $partyId): void
    {
        $parties = array_filter(MockStorage::getPartyLists(), static fn($item) => $item['party_id'] !== $partyId);
        MockStorage::setPartyLists($parties);
    }
}
