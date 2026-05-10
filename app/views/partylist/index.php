<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Party List Management</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPartyListModal">Create Party List</button>
</div>

<?php if (!empty($notice)): ?>
    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?>"><?= htmlspecialchars($notice['message']) ?></div>
<?php endif; ?>

<div class="row g-3">
    <?php if (empty($partylists)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">No party lists found.</div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($partylists as $party): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($party['party_name']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1 mb-3">
                            <?= htmlspecialchars($party['description'] !== '' ? $party['description'] : 'No description provided.') ?>
                        </p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPartyListModal<?= (int) $party['party_id'] ?>">Edit</button>
                            <form method="POST" action="<?= BASE_URL; ?>admin/partylists/delete" onsubmit="return confirm('Delete this party list?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="party_id" value="<?= (int) $party['party_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($partylists)): ?>
    <?php foreach ($partylists as $party): ?>
        <div class="modal fade" id="editPartyListModal<?= (int) $party['party_id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Party List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="<?= BASE_URL; ?>admin/partylists/update">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="party_id" value="<?= (int) $party['party_id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Party Name</label>
                                <input type="text" name="party_name" class="form-control" value="<?= htmlspecialchars($party['party_name']) ?>" maxlength="120" required>
                            </div>
                            <div>
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" maxlength="500"><?= htmlspecialchars($party['description']) ?></textarea>
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

<div class="modal fade" id="createPartyListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Party List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL; ?>admin/partylists/create">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Party Name</label>
                        <input type="text" name="party_name" class="form-control <?= !empty($errors['party_name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['party_name'] ?? '') ?>" maxlength="120" required>
                        <?php if (!empty($errors['party_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['party_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" rows="4" maxlength="500"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Party List</button>
                </div>
            </form>
        </div>
    </div>
</div>
