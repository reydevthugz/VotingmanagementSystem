<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-0">Vote Confirmation</h2>
        <p class="text-muted mb-0">Your vote has been successfully recorded.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= BASE_URL; ?>student/dashboard">Return to Dashboard</a>
</div>

<?php if (!empty($notice)): ?>
    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?>"><?= htmlspecialchars($notice['message']) ?></div>
<?php endif; ?>

<?php if (empty($votes)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted">No vote details are available.</div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Selected Candidates</h5>
            <div class="row g-3">
                <?php foreach ($votes as $vote): ?>
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-1"><?= htmlspecialchars($vote['position_name']) ?></h6>
                                <p class="mb-1 fw-semibold"><?= htmlspecialchars($vote['candidate_name']) ?></p>
                                <p class="mb-0 text-muted"><?= htmlspecialchars($vote['party_name'] ?? 'Independent') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
