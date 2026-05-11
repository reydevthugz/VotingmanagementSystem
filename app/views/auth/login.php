<div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Left Side: Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white order-2 order-lg-1">
            <div class="login-card p-4 p-md-5 shadow-lg rounded-4 w-100" style="max-width: 400px;">
                <h2 class="text-center mb-4 fw-bold text-dark">Sign In</h2>
                <?php if (!empty($notice)): ?>
                    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?> rounded-3 mb-3"><?= htmlspecialchars($notice['message']) ?></div>
                <?php endif; ?>
                <?php if (!empty($errors['auth'])): ?>
                    <div class="alert alert-danger rounded-3 mb-3"><?= htmlspecialchars($errors['auth']) ?></div>
                <?php endif; ?>
                <form method="POST" action="<?= BASE_URL; ?>login">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email / Student ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-person-fill text-muted"></i></span>
                            <input type="text" id="email" class="form-control border-start-0 rounded-end-3 <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="Enter your email or ID" required>
                        </div>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="invalid-feedback d-block mt-1"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" id="password" class="form-control border-start-0 rounded-end-3 <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" name="password" placeholder="Enter your password" required>
                            <button type="button" class="btn btn-outline-secondary border-start-0 rounded-end-3" id="togglePassword"><i class="bi bi-eye"></i></button>
                        </div>
                        <?php if (!empty($errors['password'])): ?>
                            <div class="invalid-feedback d-block mt-1"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                            <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="text-decoration-none text-primary fw-medium">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-3 login-btn">Sign In</button>
                </form>
                <p class="text-center text-muted mt-4 mb-0">Don't have an account? <a href="#" class="text-primary fw-medium text-decoration-none">Sign up</a></p>
            </div>
        </div>

        <!-- Right Side: Hero Panel -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center cyan-gradient order-1 order-lg-2 position-relative">
            <div class="hero-content text-center text-white px-4">
                <div class="hero-icon mb-4">
                    <div class="icon-circle mx-auto">
                        <i class="bi bi-check2-square display-4"></i>
                    </div>
                </div>
                <h1 class="display-5 fw-bold mb-3">Welcome Back</h1>
                <p class="lead mb-0 opacity-75">Securely access your voting dashboard and manage elections with ease.</p>
            </div>
            <!-- Decorative Shapes -->
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
</div>

