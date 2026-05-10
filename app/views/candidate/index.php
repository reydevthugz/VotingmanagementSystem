<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Candidate Management</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCandidateModal">Register Candidate</button>
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
                    <th>Photo</th>
                    <th>Full Name</th>
                    <th>Position</th>
                    <th>Party</th>
                    <th>Motto</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($candidates)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No candidates registered yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($candidates as $index => $candidate): ?>
                        <tr>
                            <td><?= (int) ($index + 1) ?></td>
                            <td>
                                <?php if (!empty($candidate['photo'])): ?>
                                    <img src="<?= BASE_URL . htmlspecialchars($candidate['photo']) ?>" alt="Candidate photo" class="rounded-circle border" style="width:60px;height:60px;object-fit:cover;">
                                <?php else: ?>
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width:60px;height:60px;">N/A</div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($candidate['fullname']) ?></td>
                            <td><?= htmlspecialchars($candidate['position_name'] ?? 'Unassigned') ?></td>
                            <td><?= htmlspecialchars($candidate['party_name'] ?? 'Unassigned') ?></td>
                            <td><?= htmlspecialchars($candidate['motto'] ?? '-') ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCandidateModal<?= (int) $candidate['candidate_id'] ?>">Edit</button>
                                    <form method="POST" action="<?= BASE_URL; ?>admin/candidates/delete" class="d-inline" onsubmit="return confirm('Delete this candidate?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="candidate_id" value="<?= (int) $candidate['candidate_id'] ?>">
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

<div class="modal fade" id="createCandidateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register Candidate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL; ?>admin/candidates/create" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control <?= !empty($errors['fullname']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['fullname'] ?? '') ?>" maxlength="150" required>
                                <?php if (!empty($errors['fullname'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['fullname']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Motto</label>
                                <textarea name="motto" class="form-control <?= !empty($errors['motto']) ? 'is-invalid' : '' ?>" rows="3" maxlength="255"><?= htmlspecialchars($old['motto'] ?? '') ?></textarea>
                                <?php if (!empty($errors['motto'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['motto']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <select name="position_id" class="form-select <?= !empty($errors['position_id']) ? 'is-invalid' : '' ?>">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($positions as $position): ?>
                                        <option value="<?= (int) $position['position_id'] ?>" <?= isset($old['position_id']) && (int) $old['position_id'] === (int) $position['position_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($position['position_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['position_id'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['position_id']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="form-label">Party List</label>
                                <select name="party_id" class="form-select <?= !empty($errors['party_id']) ? 'is-invalid' : '' ?>">
                                    <option value="">Unassigned</option>
                                    <?php foreach ($partylists as $party): ?>
                                        <option value="<?= (int) $party['party_id'] ?>" <?= isset($old['party_id']) && (int) $old['party_id'] === (int) $party['party_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($party['party_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['party_id'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['party_id']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Candidate Photo</label>
                                <input type="file" name="photo" class="form-control image-preview-input <?= !empty($errors['photo']) ? 'is-invalid' : '' ?>" accept="image/png,image/jpeg,image/gif" data-preview-target="#createCandidatePreview" required>
                                <?php if (!empty($errors['photo'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['photo']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3 text-center">
                                <img id="createCandidatePreview" class="img-fluid rounded shadow-sm" style="max-height: 240px; display: none;" alt="Photo preview">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Candidate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($candidates as $candidate): ?>
    <div class="modal fade" id="editCandidateModal<?= (int) $candidate['candidate_id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?= BASE_URL; ?>admin/candidates/update" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="candidate_id" value="<?= (int) $candidate['candidate_id'] ?>">
                        <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($candidate['photo'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($candidate['fullname']) ?>" maxlength="150" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Motto</label>
                                    <textarea name="motto" class="form-control" rows="3" maxlength="255"><?= htmlspecialchars($candidate['motto']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Position</label>
                                    <select name="position_id" class="form-select">
                                        <option value="">Unassigned</option>
                                        <?php foreach ($positions as $position): ?>
                                            <option value="<?= (int) $position['position_id'] ?>" <?= (int) $candidate['position_id'] === (int) $position['position_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($position['position_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Party List</label>
                                    <select name="party_id" class="form-select">
                                        <option value="">Unassigned</option>
                                        <?php foreach ($partylists as $party): ?>
                                            <option value="<?= (int) $party['party_id'] ?>" <?= (int) $candidate['party_id'] === (int) $party['party_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($party['party_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Change Photo</label>
                                    <input type="file" name="photo" class="form-control image-preview-input" accept="image/png,image/jpeg,image/gif" data-preview-target="#editCandidatePreview<?= (int) $candidate['candidate_id'] ?>">
                                </div>
                                <div class="text-center mt-3">
                                    <?php if (!empty($candidate['photo'])): ?>
                                        <img id="editCandidatePreview<?= (int) $candidate['candidate_id'] ?>" src="<?= BASE_URL . htmlspecialchars($candidate['photo']) ?>" alt="Current photo" class="img-fluid rounded shadow-sm" style="max-height: 240px;">
                                    <?php else: ?>
                                        <img id="editCandidatePreview<?= (int) $candidate['candidate_id'] ?>" class="img-fluid rounded shadow-sm" style="max-height: 240px; display: none;" alt="Photo preview">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Candidate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
