<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-0">Results & Reports</h2>
        <p class="text-muted mb-0">Live vote counting, candidate rankings, and printable election reports.</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button type="button" class="btn btn-outline-secondary" id="printReportButton">Print Report</button>
        <?php if (!empty($activeElection)): ?>
            <a class="btn btn-success" href="<?= BASE_URL; ?>admin/reports/export?election_id=<?= (int) $activeElection['election_id'] ?>">Export CSV</a>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-body">
        <form method="GET" class="row gy-2 gx-3 align-items-end">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Select Election</label>
                <select name="election_id" class="form-select">
                    <?php foreach ($elections as $election): ?>
                        <option value="<?= (int) $election['election_id'] ?>" <?= $activeElection && (int) $activeElection['election_id'] === (int) $election['election_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($election['title']) ?> <?= $election['status'] === 'active' ? '(Active)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">View Report</button>
            </div>
            <?php if (!empty($activeElection)): ?>
                <div class="col-auto">
                    <span class="badge bg-success">Status: <?= htmlspecialchars($activeElection['status']) ?></span>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (empty($activeElection)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted">No election selected. Choose an election to view results.</div>
    </div>
    <?php return; ?>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2">Total Votes</p>
                <h3 id="reportTotalVotes"><?= (int) $summary['total_votes'] ?></h3>
                <small class="text-muted">Votes counted in selected election</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2">Candidates</p>
                <h3 id="reportCandidateCount"><?= (int) $summary['total_candidates'] ?></h3>
                <small class="text-muted">Registered candidates</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2">Positions</p>
                <h3 id="reportPositionCount"><?= (int) $summary['total_positions'] ?></h3>
                <small class="text-muted">Vote categories</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase text-muted mb-2">Election</p>
                <h3><?= htmlspecialchars($activeElection['title']) ?></h3>
                <small class="text-muted">Report snapshot</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-xl-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">Top Candidates</h5>
                        <p class="text-muted mb-0">Live vote counting by candidate.</p>
                    </div>
                    <span class="badge bg-secondary">Auto-refresh</span>
                </div>
                <canvas id="reportChart" data-labels='<?= htmlspecialchars(json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>' data-values='<?= htmlspecialchars(json_encode($chartValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>' data-source="<?= BASE_URL; ?>admin/reports/data?election_id=<?= (int) $selectedElectionId ?>" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">Winners by Position</h5>
                <?php if (empty($positionResults)): ?>
                    <p class="text-muted">No candidate data is available for this election.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($positionResults as $position): ?>
                            <?php $winners = array_filter($position['candidates'], static fn($candidate) => $candidate['is_winner']); ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($position['position_name']) ?></h6>
                                        <small class="text-muted">Top candidate<?= count($winners) !== 1 ? 's' : '' ?></small>
                                    </div>
                                    <span class="badge bg-success"><?= count($winners) ?></span>
                                </div>
                                <?php foreach ($winners as $winner): ?>
                                    <p class="mb-1"><strong><?= htmlspecialchars($winner['candidate_name']) ?></strong> &mdash; <?= htmlspecialchars($winner['party_name']) ?></p>
                                    <small class="text-muted">Votes: <?= (int) $winner['votes'] ?></small>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="mb-4">Rankings by Position</h5>
        <?php if (empty($positionResults)): ?>
            <div class="text-muted">No ranking information available.</div>
        <?php else: ?>
            <?php foreach ($positionResults as $position): ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><?= htmlspecialchars($position['position_name']) ?></h6>
                        <span class="text-muted">Total candidates: <?= count($position['candidates']) ?></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Candidate</th>
                                    <th>Party</th>
                                    <th>Votes</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($position['candidates'] as $index => $candidate): ?>
                                    <tr>
                                        <td><?= (int) ($index + 1) ?></td>
                                        <td><?= htmlspecialchars($candidate['candidate_name']) ?></td>
                                        <td><?= htmlspecialchars($candidate['party_name']) ?></td>
                                        <td><?= (int) $candidate['votes'] ?></td>
                                        <td>
                                            <?php if ($candidate['is_winner']): ?>
                                                <span class="badge bg-success">Winner</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Runner-up</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
