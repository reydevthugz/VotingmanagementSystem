<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim(BASE_PATH, '/');
if ($basePath !== '' && ($currentPath === $basePath || str_starts_with($currentPath, $basePath . '/'))) {
    $currentPath = substr($currentPath, strlen($basePath)) ?: '/';
}
?>
<div class="sidebar-profile">
    <div class="d-flex align-items-center gap-3">
        <div class="avatar bg-white text-success"><?= strtoupper(substr($_SESSION['user']['name'] ?? 'U', 0, 1)) ?></div>
        <div>
            <p class="mb-1 text-white-50 small">Welcome back</p>
            <h6 class="text-white mb-0 fw-semibold"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?></h6>
            <span class="badge bg-white text-success mt-1 text-uppercase small"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></span>
        </div>
    </div>
</div>
<ul class="nav flex-column p-2">
    <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/dashboard' ? ' active' : '' ?>" href="<?= BASE_URL; ?>dashboard"><i class="bi bi-house-door me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/admin/elections' ? ' active' : '' ?>" href="<?= BASE_URL; ?>admin/elections"><i class="bi bi-calendar-event me-2"></i> Election Management</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/admin/positions' ? ' active' : '' ?>" href="<?= BASE_URL; ?>admin/positions"><i class="bi bi-list-task me-2"></i> Position Management</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/admin/partylists' ? ' active' : '' ?>" href="<?= BASE_URL; ?>admin/partylists"><i class="bi bi-person-badge me-2"></i> Party List Management</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/admin/candidates' ? ' active' : '' ?>" href="<?= BASE_URL; ?>admin/candidates"><i class="bi bi-person-plus me-2"></i> Candidate Management</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/admin/students' ? ' active' : '' ?>" href="<?= BASE_URL; ?>admin/students"><i class="bi bi-people me-2"></i> Student Management</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/admin/reports' ? ' active' : '' ?>" href="<?= BASE_URL; ?>admin/reports"><i class="bi bi-bar-chart me-2"></i> Results & Reports</a></li>
    <?php else: ?>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/student/dashboard' ? ' active' : '' ?>" href="<?= BASE_URL; ?>student/dashboard"><i class="bi bi-house-door me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/student/vote' ? ' active' : '' ?>" href="<?= BASE_URL; ?>student/vote"><i class="bi bi-check2-circle me-2"></i> Cast Vote</a></li>
        <li class="nav-item"><a class="nav-link text-white<?= $currentPath === '/student/vote/confirmation' ? ' active' : '' ?>" href="<?= BASE_URL; ?>student/vote/confirmation"><i class="bi bi-file-earmark-check me-2"></i> My Ballot Status</a></li>
    <?php endif; ?>
</ul>
