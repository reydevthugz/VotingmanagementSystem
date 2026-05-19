<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3">
    <div class="container-fluid p-0 align-items-center">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-success navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand fw-bold text-success d-flex align-items-center gap-2 mb-0" href="<?= BASE_URL; ?>">
                <span class="fs-5">VMS</span>
                <small class="text-muted">Admin Panel</small>
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="d-none d-md-flex align-items-center gap-2 px-2 py-1 rounded-3 bg-success-subtle">
                <i class="bi bi-person-circle fs-5 text-success"></i>
                <div class="d-flex flex-column text-end">
                    <strong class="small mb-0 text-dark"><?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></strong>
                    <span class="text-success small text-uppercase"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></span>
                </div>
            </div>
            <a href="<?= BASE_URL; ?>logout" class="btn btn-outline-success btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
        </div>
    </div>
</nav>
