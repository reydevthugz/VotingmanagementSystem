<?php
namespace App\Models;

class MockStorage
{
    private const SESSION_KEY = '_mock_data';

    public static function init(): void
    {
        if (isset($_SESSION[self::SESSION_KEY])) {
            return;
        }

        $_SESSION[self::SESSION_KEY] = [
            'users' => [
                [
                    'user_id' => 1,
                    'fullname' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password' => password_hash('admin123', PASSWORD_BCRYPT),
                    'role' => 'admin',
                    'status' => 'active',
                ],
                [
                    'user_id' => 2,
                    'fullname' => 'Student Voter',
                    'email' => 'student@example.com',
                    'password' => password_hash('student123', PASSWORD_BCRYPT),
                    'role' => 'student',
                    'status' => 'active',
                ],
            ],
            'elections' => [
                [
                    'election_id' => 1,
                    'title' => 'Student Council Election 2026',
                    'start_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'end_date' => date('Y-m-d H:i:s', strtotime('+5 days')),
                    'status' => 'active',
                ],
                [
                    'election_id' => 2,
                    'title' => 'Alumni Officer Election 2025',
                    'start_date' => date('Y-m-d H:i:s', strtotime('-90 days')),
                    'end_date' => date('Y-m-d H:i:s', strtotime('-85 days')),
                    'status' => 'inactive',
                ],
            ],
            'partylists' => [
                [
                    'party_id' => 1,
                    'party_name' => 'Blue Horizon',
                    'description' => 'A party focused on student welfare and innovation.',
                ],
                [
                    'party_id' => 2,
                    'party_name' => 'Green Future',
                    'description' => 'A party dedicated to sustainability and community service.',
                ],
                [
                    'party_id' => 3,
                    'party_name' => 'United Voices',
                    'description' => 'A party committed to inclusive representation and fairness.',
                ],
            ],
            'positions' => [
                [
                    'position_id' => 1,
                    'position_name' => 'President',
                    'max_votes' => 1,
                ],
                [
                    'position_id' => 2,
                    'position_name' => 'Vice President',
                    'max_votes' => 1,
                ],
                [
                    'position_id' => 3,
                    'position_name' => 'Treasurer',
                    'max_votes' => 1,
                ],
            ],
            'candidates' => [
                [
                    'candidate_id' => 1,
                    'fullname' => 'Ariana Santos',
                    'photo' => '',
                    'motto' => 'Leadership through empathy.',
                    'position_id' => 1,
                    'party_id' => 1,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                ],
                [
                    'candidate_id' => 2,
                    'fullname' => 'Diego Reyes',
                    'photo' => '',
                    'motto' => 'Every voice matters.',
                    'position_id' => 2,
                    'party_id' => 2,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-9 days')),
                ],
                [
                    'candidate_id' => 3,
                    'fullname' => 'Maya Cruz',
                    'photo' => '',
                    'motto' => 'A stronger school community.',
                    'position_id' => 3,
                    'party_id' => 3,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
                ],
            ],
            'students' => [
                [
                    'student_id' => 1,
                    'fullname' => 'John Doe',
                    'course' => 'BS Computer Science',
                    'year' => '2nd Year',
                    'section' => 'A',
                    'email' => 'johndoe@example.com',
                ],
                [
                    'student_id' => 2,
                    'fullname' => 'Jane Smith',
                    'course' => 'BS Information Technology',
                    'year' => '3rd Year',
                    'section' => 'B',
                    'email' => 'janesmith@example.com',
                ],
                [
                    'student_id' => 3,
                    'fullname' => 'Leo Martinez',
                    'course' => 'BS Business Administration',
                    'year' => '1st Year',
                    'section' => 'C',
                    'email' => 'leomartinez@example.com',
                ],
            ],
            'votes' => [
                [
                    'vote_id' => 1,
                    'student_id' => 2,
                    'candidate_id' => 1,
                    'position_id' => 1,
                    'election_id' => 2,
                    'voted_at' => date('Y-m-d H:i:s', strtotime('-85 days')),
                ],
            ],
            'activity_logs' => [],
        ];
    }

    public static function getUsers(): array
    {
        self::init();
        return $_SESSION[self::SESSION_KEY]['users'];
    }

    public static function setUsers(array $users): void
    {
        $_SESSION[self::SESSION_KEY]['users'] = array_values($users);
    }

    public static function findUserByEmail(string $email): ?array
    {
        foreach (self::getUsers() as $user) {
            if (strcasecmp($user['email'], $email) === 0) {
                return $user;
            }
        }
        return null;
    }

    public static function updateUserPasswordHash(int $userId, string $hashedPassword): void
    {
        $users = self::getUsers();
        foreach ($users as &$user) {
            if ($user['user_id'] === $userId) {
                $user['password'] = $hashedPassword;
                break;
            }
        }
        self::setUsers($users);
    }

    public static function getElections(): array
    {
        self::init();
        return $_SESSION[self::SESSION_KEY]['elections'];
    }

    public static function setElections(array $elections): void
    {
        $_SESSION[self::SESSION_KEY]['elections'] = array_values($elections);
    }

    public static function getPartyLists(): array
    {
        self::init();
        return $_SESSION[self::SESSION_KEY]['partylists'];
    }

    public static function setPartyLists(array $partylists): void
    {
        $_SESSION[self::SESSION_KEY]['partylists'] = array_values($partylists);
    }

    public static function getPositions(): array
    {
        self::init();
        return $_SESSION[self::SESSION_KEY]['positions'];
    }

    public static function setPositions(array $positions): void
    {
        $_SESSION[self::SESSION_KEY]['positions'] = array_values($positions);
    }

    public static function getCandidates(): array
    {
        self::init();
        return $_SESSION[self::SESSION_KEY]['candidates'];
    }

    public static function setCandidates(array $candidates): void
    {
        $_SESSION[self::SESSION_KEY]['candidates'] = array_values($candidates);
    }

    public static function getStudents(): array
    {
        self::init();
        return $_SESSION[self::SESSION_KEY]['students'];
    }

    public static function setStudents(array $students): void
    {
        $_SESSION[self::SESSION_KEY]['students'] = array_values($students);
    }

    public static function getVotes(): array
    {
        self::init();
        return $_SESSION[self::SESSION_KEY]['votes'];
    }

    public static function setVotes(array $votes): void
    {
        $_SESSION[self::SESSION_KEY]['votes'] = array_values($votes);
    }

    public static function addActivityLog(array $log): void
    {
        self::init();
        $_SESSION[self::SESSION_KEY]['activity_logs'][] = $log;
    }

    public static function nextId(string $collection): int
    {
        $items = match ($collection) {
            'users' => self::getUsers(),
            'elections' => self::getElections(),
            'partylists' => self::getPartyLists(),
            'positions' => self::getPositions(),
            'candidates' => self::getCandidates(),
            'students' => self::getStudents(),
            'votes' => self::getVotes(),
            default => [],
        };

        $maxId = 0;
        foreach ($items as $item) {
            $key = array_key_exists('vote_id', $item) ? 'vote_id' : (array_key_exists('student_id', $item) ? 'student_id' : (array_key_exists('candidate_id', $item) ? 'candidate_id' : (array_key_exists('position_id', $item) ? 'position_id' : (array_key_exists('party_id', $item) ? 'party_id' : (array_key_exists('election_id', $item) ? 'election_id' : (array_key_exists('user_id', $item) ? 'user_id' : 0))))));
            $maxId = max($maxId, (int) ($item[$key] ?? 0));
        }

        return $maxId + 1;
    }
}
