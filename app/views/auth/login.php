<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="text-center text-success mb-4">System Login</h4>
                    <?php if (!empty($notice)): ?>
                        <div class="alert alert-<?= htmlspecialchars($notice['type']) ?>"><?= htmlspecialchars($notice['message']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($errors['auth'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errors['auth']) ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?= BASE_URL; ?>login">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                            <?php if (!empty($errors['email'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" name="password" required>
                            <?php if (!empty($errors['password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
