<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-0">Student Management</h2>
        <p class="text-muted mb-0">Register, import, and manage student accounts.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importStudentsModal">Import Students</button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createStudentModal">Register Student</button>
    </div>
</div>

<?php if (!empty($notice)): ?>
    <div class="alert alert-<?= htmlspecialchars($notice['type']) ?>"><?= htmlspecialchars($notice['message']) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row gx-3 gy-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label">Search</label>
                <input type="search" name="search" class="form-control" value="<?= htmlspecialchars($filters['query'] ?? '') ?>" placeholder="Search name, email, course, section">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Course</label>
                <select name="course" class="form-select">
                    <option value="">All courses</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course) ?>" <?= ($filters['course'] ?? '') === $course ? 'selected' : '' ?>><?= htmlspecialchars($course) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Year</label>
                <select name="year" class="form-select">
                    <option value="">All years</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= htmlspecialchars($year) ?>" <?= ($filters['year'] ?? '') === $year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Section</label>
                <select name="section" class="form-select">
                    <option value="">All sections</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?= htmlspecialchars($section) ?>" <?= ($filters['section'] ?? '') === $section ? 'selected' : '' ?>><?= htmlspecialchars($section) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Section</th>
                    <th>Email</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No students found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?= (int) (($page - 1) * 10 + $index + 1) ?></td>
                            <td><?= htmlspecialchars($student['fullname']) ?></td>
                            <td><?= htmlspecialchars($student['course']) ?></td>
                            <td><?= htmlspecialchars($student['year']) ?></td>
                            <td><?= htmlspecialchars($student['section']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStudentModal<?= (int) $student['student_id'] ?>">Edit</button>
                                    <form method="POST" action="<?= BASE_URL; ?>admin/students/delete" class="d-inline" onsubmit="return confirm('Delete this student?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="student_id" value="<?= (int) $student['student_id'] ?>">
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

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-3">
            <div class="text-muted">Showing <?= htmlspecialchars(count($students)) ?> of <?= htmlspecialchars($totalStudents) ?> students</div>
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination mb-0">
                        <?php
                        $baseParams = array_filter([
                            'search' => $filters['query'] ?? '',
                            'course' => $filters['course'] ?? '',
                            'year' => $filters['year'] ?? '',
                            'section' => $filters['section'] ?? '',
                        ], fn($value) => $value !== '');
                        ?>
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($baseParams, ['page' => $page - 1])) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($baseParams, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($baseParams, ['page' => $page + 1])) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="createStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL; ?>admin/students/create">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control <?= !empty($errors['fullname']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['fullname'] ?? '') ?>" maxlength="150" required>
                        <?php if (!empty($errors['fullname'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['fullname']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <input type="text" name="course" class="form-control <?= !empty($errors['course']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['course'] ?? '') ?>" maxlength="100" required>
                        <?php if (!empty($errors['course'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['course']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <input type="text" name="year" class="form-control <?= !empty($errors['year']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['year'] ?? '') ?>" maxlength="20" required>
                        <?php if (!empty($errors['year'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['year']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <input type="text" name="section" class="form-control <?= !empty($errors['section']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['section'] ?? '') ?>" maxlength="50" required>
                        <?php if (!empty($errors['section'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['section']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($old['email'] ?? '') ?>" maxlength="150" required>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importStudentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Student List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL; ?>admin/students/import" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" name="import_file" class="form-control" accept=".csv" required>
                        <div class="form-text">Expected columns: fullname, course, year, section, email.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Import Students</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($students as $student): ?>
    <div class="modal fade" id="editStudentModal<?= (int) $student['student_id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?= BASE_URL; ?>admin/students/update">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="<?= (int) $student['student_id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($student['fullname']) ?>" maxlength="150" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <input type="text" name="course" class="form-control" value="<?= htmlspecialchars($student['course']) ?>" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="text" name="year" class="form-control" value="<?= htmlspecialchars($student['year']) ?>" maxlength="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control" value="<?= htmlspecialchars($student['section']) ?>" maxlength="50" required>
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" maxlength="150" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
