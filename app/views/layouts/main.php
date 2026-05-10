<!DOCTYPE html>
<html lang="en">
<head>
    <?php require base_path('app/views/partials/header.php'); ?>
</head>
<body class="bg-surface">
<div class="container-fluid">
    <div class="row min-vh-100">
        <?php if (!empty($_SESSION['user'])): ?>
            <aside class="col-12 col-md-3 col-lg-2 p-0 d-none d-md-block sidebar-desktop">
                <?php require base_path('app/views/partials/sidebar.php'); ?>
            </aside>

            <div class="offcanvas offcanvas-start text-white sidebar-offcanvas" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
                <div class="offcanvas-header border-bottom border-white-25">
                    <h5 class="offcanvas-title text-white" id="sidebarOffcanvasLabel">Navigation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <?php require base_path('app/views/partials/sidebar.php'); ?>
                </div>
            </div>

            <main class="col-12 col-md-9 col-lg-10 p-0 main-content-mobile">
                <?php require base_path('app/views/partials/navbar.php'); ?>
                <section class="p-3 p-md-4">
                    <?php require $viewFile; ?>
                </section>
            </main>
        <?php else: ?>
            <main class="col-12 p-0"><?php require $viewFile; ?></main>
        <?php endif; ?>
    </div>
</div>
<?php require base_path('app/views/partials/footer.php'); ?>
</body>
</html>
 