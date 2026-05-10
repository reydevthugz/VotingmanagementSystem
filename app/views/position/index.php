<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Position Management</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPositionModal">Add Position</button>
</div>

<?php if (!empty($notice)): ?>
    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?>"><?= htmlspecialchars($notice['message']) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Position Name</th>
                    <th>Maximum Votes</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($positions)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No positions found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($positions as $index => $position): ?>
                        <tr>
                            <td><?= (int) ($index + 1) ?></td>
                            <td><?= htmlspecialchars($position['position_name']) ?></td>
                            <td><?= (int) $position['max_votes'] ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPositionModal<?= (int) $position['position_id'] ?>">Edit</button>
                                    <form method="POST" action="<?= BASE_URL; ?>admin/positions/delete" class="d-inline" onsubmit="return confirm('Delete this position?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="position_id" value="<?= (int) $position['position_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($positions)): ?>
    <?php foreach ($positions as $position): ?>
        <div class="modal fade" id="editPositionModal<?= (int) $position['position_id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="<?= BASE_URL; ?>admin/positions/update">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="position_id" value="<?= (int) $position['position_id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Position Name</label>
                                <input type="text" name="position_name" class="form-control" value="<?= htmlspecialchars($position['position_name']) ?>" required>
                            </div>
                            <div>
                                <label class="form-label">Maximum Votes</label>
                                <input type="number" min="1" name="max_votes" class="form-control" value="<?= (int) $position['max_votes'] ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="modal fade" id="createPositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL; ?>admin/positions/create">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Position Name</label>
                        <input type="text" name="position_name" class="form-control <?= !empty($errors['position_name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['position_name'] ?? '') ?>" required>
                        <?php if (!empty($errors['position_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['position_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form-label">Maximum Votes</label>
                        <input type="number" min="1" name="max_votes" class="form-control <?= !empty($errors['max_votes']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['max_votes'] ?? '1') ?>" required>
                        <?php if (!empty($errors['max_votes'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['max_votes']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Position</button>
                </div>
            </form>
        </div>
    </div>
</div>
