<?php
$userName = isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : 'Student';
?>
<div class="mb-4">
    <h1 class="display-5 fw-bold"><i class="bi bi-person-circle me-2"></i> Welcome, <?= $userName ?>!</h1>
    <p class="lead text-muted">Here's your student dashboard.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="mb-3">You can proceed to the voting module once elections are active.</p>
        <a href="<?= BASE_URL; ?>student/vote" class="btn btn-success btn-lg"><i class="bi bi-check2-circle me-2"></i>Cast My Vote</a>
    </div>
</div>
