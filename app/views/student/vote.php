<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-0">Cast Your Vote</h2>
        <?php if (!empty($activeElection)): ?>
            <p class="text-muted mb-0">Election: <?= htmlspecialchars($activeElection['title']) ?></p>
        <?php endif; ?>
    </div>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL; ?>student/dashboard">Back to Dashboard</a>
</div>

<?php if (!empty($notice)): ?>
    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?>"><?= htmlspecialchars($notice['message']) ?></div>
<?php endif; ?>

<?php if (empty($activeElection)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
            <h5 class="mb-3">No active election available.</h5>
            <p class="text-muted">Please check back later when an election has been activated.</p>
        </div>
    </div>
    <?php return; ?>
<?php endif; ?>

<?php if (!empty($hasVoted)): ?>
    <div class="alert alert-success">You have already submitted your vote for this election.</div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL; ?>student/vote/submit">
    <?= csrf_field() ?>
    <?php if (empty($positions)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="mb-0 text-muted">There are currently no positions configured for voting.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($positions as $position): ?>
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="h5 mb-1"><?= htmlspecialchars($position['position_name']) ?></h3>
                        <small class="text-muted">Choose one candidate for this position.</small>
                    </div>
                    <span class="badge bg-secondary">1 vote</span>
                </div>
                <?php if (empty($position['candidates'])): ?>
                    <div class="alert alert-warning">No candidates are available for this position.</div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($position['candidates'] as $candidate): ?>
                            <?php $checked = isset($votes[$position['position_id']]) && $votes[$position['position_id']] === $candidate['candidate_id']; ?>
                            <div class="col-12 col-md-6 col-xl-4">
                                <label class="card vote-card <?= $checked ? 'selected' : '' ?> h-100">
                                    <input type="radio" name="vote_<?= (int) $position['position_id'] ?>" value="<?= (int) $candidate['candidate_id'] ?>" <?= $checked ? 'checked' : '' ?> <?= $hasVoted ? 'disabled' : '' ?> class="vote-input">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <?php if (!empty($candidate['photo'])): ?>
                                                    <img src="<?= BASE_URL . htmlspecialchars($candidate['photo']) ?>" alt="<?= htmlspecialchars($candidate['fullname']) ?>" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" style="width:80px;height:80px;">N/A</div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h5 class="mb-1"><?= htmlspecialchars($candidate['fullname']) ?></h5>
                                                <p class="mb-1 text-muted small"><?= htmlspecialchars($candidate['party_name']) ?></p>
                                                <p class="mb-0 text-truncate" style="max-height: 3.5rem; overflow: hidden;"><?= htmlspecialchars($candidate['motto'] ?: 'No motto provided.') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-0 text-end">
                                        <span class="badge bg-success">Select</span>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <a class="btn btn-secondary" href="<?= BASE_URL; ?>student/dashboard">Cancel</a>
        <button type="submit" class="btn btn-success" <?= !empty($hasVoted) ? 'disabled' : '' ?>>Submit Vote</button>
    </div>
</form>
