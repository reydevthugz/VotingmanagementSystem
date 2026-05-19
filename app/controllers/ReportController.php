<?php
namespace App\Controllers;

use App\Models\Election;
use App\Models\Report;
use Throwable;

class ReportController extends BaseController
{
    public function index(): void
    {
        $reportModel = new Report();
        $elections = $reportModel->elections();
        $selectedElectionId = (int) ($_GET['election_id'] ?? 0);

        if ($selectedElectionId <= 0 && !empty($elections)) {
            $selectedElectionId = (int) $elections[0]['election_id'];
        }

        $activeElection = null;
        $positionResults = [];
        $chartLabels = [];
        $chartValues = [];
        $summary = [
            'total_votes' => 0,
            'total_candidates' => 0,
            'total_positions' => 0,
        ];

        if ($selectedElectionId > 0) {
            $activeElection = $reportModel->electionById($selectedElectionId);
            if ($activeElection) {
                $summary['total_votes'] = $reportModel->totalVotes($selectedElectionId);
                $summary['total_candidates'] = $reportModel->totalCandidates($selectedElectionId);
                $summary['total_positions'] = $reportModel->totalPositions($selectedElectionId);

                $candidateResults = $reportModel->candidateResults($selectedElectionId);
                $positionResults = $this->groupResultsByPosition($candidateResults);
                $topCandidates = array_slice($this->topCandidates($candidateResults), 0, 8);
                $chartLabels = array_column($topCandidates, 'label');
                $chartValues = array_column($topCandidates, 'votes');
            }
        }

        $this->render('admin/report', [
            'pageTitle' => 'Results & Reports',
            'elections' => $elections,
            'activeElection' => $activeElection,
            'positionResults' => $positionResults,
            'summary' => $summary,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'selectedElectionId' => $selectedElectionId,
            'notice' => flash(),
        ]);
    }

    public function data(): void
    {
        $selectedElectionId = (int) ($_GET['election_id'] ?? 0);
        if ($selectedElectionId <= 0) {
            $this->json(['ok' => false, 'message' => 'Election not selected.'], 400);
        }

        try {
            $reportModel = new Report();
            $election = $reportModel->electionById($selectedElectionId);
            if (!$election) {
                $this->json(['ok' => false, 'message' => 'Election not found.'], 404);
            }

            $summary = [
                'total_votes' => $reportModel->totalVotes($selectedElectionId),
                'total_candidates' => $reportModel->totalCandidates($selectedElectionId),
                'total_positions' => $reportModel->totalPositions($selectedElectionId),
            ];

            $candidateResults = $reportModel->candidateResults($selectedElectionId);
            $topCandidates = array_slice($this->topCandidates($candidateResults), 0, 8);

            $this->json([
                'ok' => true,
                'summary' => $summary,
                'topCandidates' => $topCandidates,
            ]);
        } catch (Throwable $exception) {
            $this->json(['ok' => false, 'message' => 'Unable to load report data.'], 500);
        }
    }

    public function export(): void
    {
        $selectedElectionId = (int) ($_GET['election_id'] ?? 0);
        if ($selectedElectionId <= 0) {
            $this->flash('danger', 'Election is required for export.');
            $this->redirect('/admin/reports');
        }

        $reportModel = new Report();
        $election = $reportModel->electionById($selectedElectionId);
        if (!$election) {
            $this->flash('danger', 'Election not found.');
            $this->redirect('/admin/reports');
        }

        $filename = sprintf('election-report-%s.csv', date('Ymd_His'));
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Election', $election['title']]);
        fputcsv($output, []);
        fputcsv($output, ['Position', 'Candidate', 'Party', 'Votes']);

        $candidateResults = $reportModel->candidateResults($selectedElectionId);
        foreach ($candidateResults as $result) {
            if ($result['candidate_id'] === null) {
                continue;
            }
            fputcsv($output, [
                $result['position_name'],
                $result['candidate_name'],
                $result['party_name'] ?? 'Independent',
                (int) $result['votes'],
            ]);
        }

        fclose($output);
        exit;
    }

    private function groupResultsByPosition(array $candidateResults): array
    {
        $positions = [];
        foreach ($candidateResults as $result) {
            $positionId = (int) $result['position_id'];
            if (!isset($positions[$positionId])) {
                $positions[$positionId] = [
                    'position_id' => $positionId,
                    'position_name' => $result['position_name'],
                    'candidates' => [],
                    'winner_votes' => 0,
                ];
            }

            $votes = (int) $result['votes'];
            $positions[$positionId]['candidates'][] = [
                'candidate_id' => $result['candidate_id'],
                'candidate_name' => $result['candidate_name'],
                'party_name' => $result['party_name'] ?? 'Independent',
                'photo' => $result['photo'] ?? '',
                'votes' => $votes,
                'motto' => $result['motto'] ?? '',
                'is_winner' => false,
            ];

            if ($votes > $positions[$positionId]['winner_votes']) {
                $positions[$positionId]['winner_votes'] = $votes;
            }
        }

        foreach ($positions as $positionId => $position) {
            foreach ($position['candidates'] as &$candidate) {
                if ($candidate['votes'] === $position['winner_votes'] && $candidate['candidate_id'] !== null) {
                    $candidate['is_winner'] = true;
                }
            }
            usort($positions[$positionId]['candidates'], static fn($a, $b) => $b['votes'] <=> $a['votes']);
        }

        return array_values($positions);
    }

    private function topCandidates(array $candidateResults): array
    {
        $top = [];
        foreach ($candidateResults as $result) {
            if ($result['candidate_id'] === null) {
                continue;
            }
            $top[] = [
                'label' => sprintf('%s (%s)', $result['candidate_name'], $result['position_name']),
                'votes' => (int) $result['votes'],
            ];
        }

        usort($top, static fn($a, $b) => $b['votes'] <=> $a['votes']);
        return $top;
    }
}
