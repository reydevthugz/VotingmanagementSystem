<?php
$userName = isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : 'Admin';
?>
<div class="mb-4">
    <h1 class="display-5 fw-bold">Welcome, <?= $userName ?>!</h1>
    <p class="lead text-muted">Here's an overview of the voting system.</p>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Dashboard</h2>
    <span class="badge bg-success">Milestone 12</span>
</div>

<div id="adminDashboardRoot" data-stats-url="/dashboard/stats" data-chart-url="/dashboard/charts/votes-trend"></div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Voters</p>
                        <h3 id="cardVoters" class="mb-0">0</h3>
                    </div>
                    <div class="fs-3">
                        <i class="bi bi-people text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Candidates</p>
                        <h3 id="cardCandidates" class="mb-0">0</h3>
                    </div>
                    <div class="fs-3">
                        <i class="bi bi-person-badge text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Votes Cast</p>
                        <h3 id="cardVotes" class="mb-0">0</h3>
                    </div>
                    <div class="fs-3">
                        <i class="bi bi-box-arrow-in-down text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Election Status</p>
                        <h3 id="cardStatus" class="mb-0 text-capitalize">Inactive</h3>
                    </div>
                    <div class="fs-3">
                        <i class="bi bi-calendar-check text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h3 class="h6 mb-0">Vote Trend (Last 7 Days)</h3>
            </div>
            <div class="card-body">
                <canvas id="votesTrendChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pb-0">
                <h3 class="h6 mb-0">System Snapshot</h3>
            </div>
            <div class="card-body">
                <p class="mb-1">Dashboard cards and chart are loaded from API endpoints.</p>
                <small class="text-muted">Useful for future SPA components and analytics widgets.</small>
            </div>
        </div>
    </div>
</div>
