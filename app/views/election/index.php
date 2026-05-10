<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Election Management</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createElectionModal">Create Election</button>
</div>

<?php if (!empty($notice)): ?>
    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?>"><?= htmlspecialchars($notice['message']) ?></div>
<?php endif; ?>

<?php if (!empty($errors['date_range'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errors['date_range']) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($elections)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No elections found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($elections as $index => $election): ?>
                        <tr>
                            <td><?= (int) ($index + 1) ?></td>
                            <td><?= htmlspecialchars($election['title']) ?></td>
                            <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($election['start_date']))) ?></td>
                            <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($election['end_date']))) ?></td>
                            <td>
                                <span class="badge bg-<?= $election['status'] === 'active' ? 'success' : 'secondary' ?> text-capitalize">
                                    <?= htmlspecialchars($election['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editElectionModal<?= (int) $election['election_id'] ?>">Edit</button>
                                    <?php if ($election['status'] === 'active'): ?>
                                        <form method="POST" action="<?= BASE_URL; ?>admin/elections/deactivate" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="election_id" value="<?= (int) $election['election_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Deactivate</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="<?= BASE_URL; ?>admin/elections/activate" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="election_id" value="<?= (int) $election['election_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success">Activate</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?= BASE_URL; ?>admin/elections/delete" class="d-inline" onsubmit="return confirm('Delete this election?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="election_id" value="<?= (int) $election['election_id'] ?>">
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

<?php if (!empty($elections)): ?>
    <?php foreach ($elections as $election): ?>
        <div class="modal fade" id="editElectionModal<?= (int) $election['election_id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Election</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="<?= BASE_URL; ?>admin/elections/update">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="election_id" value="<?= (int) $election['election_id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($election['title']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local" name="start_date" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($election['start_date']))) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="datetime-local" name="end_date" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($election['end_date']))) ?>" required>
                            </div>
                            <div>
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="inactive" <?= $election['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="active" <?= $election['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                </select>
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

<div class="modal fade" id="createElectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Election</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL; ?>admin/elections/create">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>
                        <?php if (!empty($errors['title'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control <?= !empty($errors['start_date']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['start_date'] ?? '') ?>" required>
                        <?php if (!empty($errors['start_date'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['start_date']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="datetime-local" name="end_date" class="form-control <?= !empty($errors['end_date']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['end_date'] ?? '') ?>" required>
                        <?php if (!empty($errors['end_date'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['end_date']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="inactive" <?= ($old['status'] ?? 'inactive') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="active" <?= ($old['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Election</button>
                </div>
            </form>
        </div>
    </div>
</div>
