<?php
namespace App\Controllers;

use App\Models\Vote;
use Throwable;

class VoteController extends BaseController
{
    public function index(): void
    {
        $voteModel = new Vote();
        $activeElection = $voteModel->activeElection();

        if (!$activeElection) {
            $this->render('student/vote', [
                'pageTitle' => 'Vote',
                'activeElection' => null,
                'positions' => [],
                'votes' => [],
                'hasVoted' => false,
                'notice' => flash(),
            ]);
            return;
        }

        $studentId = (int) ($_SESSION['user']['id'] ?? 0);
        $positions = $voteModel->ballotByPosition();
        $votes = $voteModel->studentVotes($studentId, (int) $activeElection['election_id']);
        $hasVoted = $voteModel->hasVotedInElection($studentId, (int) $activeElection['election_id']);

        $this->render('student/vote', [
            'pageTitle' => 'Vote',
            'activeElection' => $activeElection,
            'positions' => $positions,
            'votes' => $votes,
            'hasVoted' => $hasVoted,
            'notice' => flash(),
        ]);
    }

    public function submit(): void
    {
        $studentId = (int) ($_SESSION['user']['id'] ?? 0);
        $voteModel = new Vote();
        $activeElection = $voteModel->activeElection();

        if (!$activeElection) {
            $this->flash('danger', 'No active election is available at this time.');
            $this->redirect('/student/vote');
        }

        $electionId = (int) $activeElection['election_id'];
        if ($voteModel->hasVotedInElection($studentId, $electionId)) {
            $this->flash('danger', 'You have already submitted your vote for this election.');
            $this->redirect('/student/vote');
        }

        $positions = $voteModel->ballotByPosition();
        $selected = [];
        $errors = [];
        $ballotCount = 0;

        foreach ($positions as $position) {
            if (empty($position['candidates'])) {
                continue;
            }
            $ballotCount++;

            $key = 'vote_' . $position['position_id'];
            $candidateId = isset($_POST[$key]) ? (int) $_POST[$key] : 0;
            if ($candidateId <= 0) {
                $errors[] = sprintf('Please select a candidate for %s.', $position['position_name']);
                continue;
            }
            $selected[$position['position_id']] = $candidateId;
        }

        if ($ballotCount === 0) {
            $this->flash('danger', 'There are no available positions to vote for at this moment.');
            $this->redirect('/student/vote');
        }

        if (!empty($errors)) {
            $this->flash('danger', implode(' ', $errors));
            $this->redirect('/student/vote');
        }

        $validatedPositions = [];
        foreach ($selected as $positionId => $candidateId) {
            $candidate = $voteModel->candidateById($candidateId);
            if (!$candidate || (int) $candidate['position_id'] !== $positionId) {
                $this->flash('danger', 'Invalid candidate selection.');
                $this->redirect('/student/vote');
            }
            if (isset($validatedPositions[$positionId])) {
                $this->flash('danger', 'Duplicate vote detected for one or more positions.');
                $this->redirect('/student/vote');
            }
            $validatedPositions[$positionId] = true;
        }

        try {
            foreach ($selected as $positionId => $candidateId) {
                $voteModel->createVote($studentId, $candidateId, $positionId, $electionId);
            }
            $this->recordActivity('vote_submit', ['student_id' => $studentId, 'election_id' => $electionId, 'positions' => array_keys($selected)]);
            $this->flash('success', 'Your vote has been submitted successfully. Thank you for voting.');
            $this->redirect('/student/vote/confirmation');
        } catch (Throwable $exception) {
            $this->flash('danger', 'Unable to submit your vote. Please try again.');
            $this->redirect('/student/vote');
        }
    }

    public function confirmation(): void
    {
        $voteModel = new Vote();
        $activeElection = $voteModel->activeElection();

        if (!$activeElection) {
            $this->flash('danger', 'No active election is available at this time.');
            $this->redirect('/student/vote');
        }

        $studentId = (int) ($_SESSION['user']['id'] ?? 0);
        $votes = $voteModel->confirmationDetails($studentId, (int) $activeElection['election_id']);

        $this->render('student/vote_confirmation', [
            'pageTitle' => 'Vote Confirmation',
            'activeElection' => $activeElection,
            'votes' => $votes,
            'notice' => flash(),
        ]);
    }
}
