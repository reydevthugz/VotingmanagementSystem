<div class="container-fluid vh-100">
    <div class="row h-100">
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white order-2 order-lg-1">
            <div class="register-card p-4 p-md-5 shadow-lg rounded-4 w-100" style="max-width: 480px;">
                <h2 class="fw-bold mb-2">Create Student Account</h2>
                <p class="text-muted mb-4">Register to participate in upcoming campus elections.</p>
                <?php if (!empty($notice)): ?>
                    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?> rounded-3 mb-3"><?= htmlspecialchars($notice['message']) ?></div>
                <?php endif; ?>
                <form method="POST" action="<?= BASE_URL; ?>register">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="student_id" class="form-label fw-semibold">Student ID</label>
                            <input type="text" id="student_id" name="student_id" class="form-control <?= !empty($errors['student_id']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['student_id'] ?? '') ?>" placeholder="e.g. 2023-0001" required>
                            <?php if (!empty($errors['student_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['student_id']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label for="fullname" class="form-label fw-semibold">Full Name</label>
                            <input type="text" id="fullname" name="fullname" class="form-control <?= !empty($errors['fullname']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['fullname'] ?? '') ?>" placeholder="Your full name" required>
                            <?php if (!empty($errors['fullname'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['fullname']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <label for="year" class="form-label fw-semibold">Year</label>
                            <select id="year" name="year" class="form-select <?= !empty($errors['year']) ? 'is-invalid' : '' ?>" required>
                                <option value="">Select year</option>
                                <option value="1st Year" <?= (htmlspecialchars($old['year'] ?? '') === '1st Year') ? 'selected' : '' ?>>1st Year</option>
                                <option value="2nd Year" <?= (htmlspecialchars($old['year'] ?? '') === '2nd Year') ? 'selected' : '' ?>>2nd Year</option>
                                <option value="3rd Year" <?= (htmlspecialchars($old['year'] ?? '') === '3rd Year') ? 'selected' : '' ?>>3rd Year</option>
                                <option value="4th Year" <?= (htmlspecialchars($old['year'] ?? '') === '4th Year') ? 'selected' : '' ?>>4th Year</option>
                            </select>
                            <?php if (!empty($errors['year'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['year']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <label for="section" class="form-label fw-semibold">Section</label>
                            <input type="text" id="section" name="section" class="form-control <?= !empty($errors['section']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['section'] ?? '') ?>" placeholder="e.g. A" required>
                            <?php if (!empty($errors['section'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['section']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="student@university.edu.ph" required>
                            <?php if (!empty($errors['email'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" id="password" name="password" class="form-control border-start-0 rounded-end-3 <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Create password" required>
                                <button type="button" class="btn btn-outline-secondary border-start-0 rounded-end-3" data-password-toggle="password"><i class="bi bi-eye"></i></button>
                            </div>
                            <?php if (!empty($errors['password'])): ?>
                                <div class="invalid-feedback d-block mt-1"><?= htmlspecialchars($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control border-start-0 rounded-end-3 <?= !empty($errors['password_confirmation']) ? 'is-invalid' : '' ?>" placeholder="Confirm password" required>
                                <button type="button" class="btn btn-outline-secondary border-start-0 rounded-end-3" data-password-toggle="password_confirmation"><i class="bi bi-eye"></i></button>
                            </div>
                            <?php if (!empty($errors['password_confirmation'])): ?>
                                <div class="invalid-feedback d-block mt-1"><?= htmlspecialchars($errors['password_confirmation']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input <?= !empty($errors['terms']) ? 'is-invalid' : '' ?>" type="checkbox" id="terms" name="terms">
                                <label class="form-check-label text-muted" for="terms">I agree to the <a href="#" class="text-primary text-decoration-none">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none">Privacy Policy</a></label>
                            </div>
                            <?php if (!empty($errors['terms'])): ?>
                                <div class="invalid-feedback d-block mt-1"><?= htmlspecialchars($errors['terms']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-4 rounded-3 register-btn">Register Account</button>
                </form>
                <p class="text-center text-muted mt-4 mb-0">Already have an account? <a href="<?= BASE_URL; ?>login" class="text-primary fw-medium text-decoration-none">Sign in here</a></p>
            </div>
        </div>
        <div class="col-lg-6 d-flex align-items-center justify-content-center cyan-gradient order-1 order-lg-2 position-relative">
            <div class="hero-content text-center text-white px-4 position-relative">
                <a href="<?= BASE_URL; ?>" class="text-white text-decoration-none position-absolute top-0 end-0 mt-4 me-4 small fw-semibold">Back to Home <i class="bi bi-arrow-right"></i></a>
                <div class="hero-icon mb-4">
                    <div class="icon-circle mx-auto">
                        <i class="bi bi-check2-square display-4"></i>
                    </div>
                </div>
                <h1 class="display-5 fw-bold mb-3">Join the Election</h1>
                <p class="lead mb-0 opacity-75">Your vote is your voice. Register now to participate in shaping the future of your campus.</p>
            </div>
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
</div>
