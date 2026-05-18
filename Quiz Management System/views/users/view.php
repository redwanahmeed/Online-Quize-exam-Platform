<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- User Information Card -->
            <div class="card">
                <div class="card-header">
                    <h5>User Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <?php if ($user['profile_image'] && file_exists(__DIR__ . '/../../assets/uploads/' . $user['profile_image'])): ?>
                            <img src="assets/uploads/<?php echo $user['profile_image']; ?>" 
                                 class="rounded-circle" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                                 style="width: 120px; height: 120px; font-size: 48px;">
                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 35%;">User ID</th>
                            <td><?php echo $user['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td><?php echo $user['phone'] ?? 'Not provided'; ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                <?php
                                $role_badge = [
                                    'admin' => 'danger',
                                    'instructor' => 'primary',
                                    'teaching_assistant' => 'warning',
                                    'student' => 'success'
                                ];
                                $badge_color = $role_badge[$user['role']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badge_color; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo $user['address'] ?? 'Not provided'; ?></td>
                        </tr>
                        <tr>
                            <th>Member Since</th>
                            <td><?php echo date('F d, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <tr>
                            <th>Last Login</th>
                            <td><?php echo $user['last_login'] ? date('F d, Y h:i A', strtotime($user['last_login'])) : 'First login'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <?php if ($user['role'] == 'student' && $student): ?>
                <!-- Student Information Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>Student Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 35%;">Student ID</th>
                                <td><?php echo $student['student_id']; ?></td>
                            </tr>
                            <tr>
                                <th>Course</th>
                                <td><?php echo $student['course'] ?? 'Not assigned'; ?></td>
                            </tr>
                            <tr>
                                <th>Year Level</th>
                                <td><?php echo $student['year_level'] ?? 'Not assigned'; ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-<?php echo $student['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($student['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Student Address</th>
                                <td><?php echo $student['address'] ?? 'Not provided'; ?></td>
                            </tr>
                            <tr>
                                <th>Student Phone</th>
                                <td><?php echo $student['phone'] ?? 'Not provided'; ?></td>
                            </tr>
                            <tr>
                                <th>Enrolled Since</th>
                                <td><?php echo date('F d, Y', strtotime($student['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Activity Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Account Activity</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3><?php 
                                    // Count courses created by this user (if instructor)
                                    echo '0'; 
                                ?></h3>
                                <small class="text-muted">Courses</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3><?php 
                                    // Count students assigned (if instructor)
                                    echo '0'; 
                                ?></h3>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3><?php 
                                    // Days since registration
                                    $created = new DateTime($user['created_at']);
                                    $now = new DateTime();
                                    $diff = $created->diff($now);
                                    echo $diff->days;
                                ?></h3>
                                <small class="text-muted">Days Active</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <a href="index.php?page=users" class="btn btn-secondary">
                Back to Users
            </a>
            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editRoleModal" 
                        onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['role']; ?>')">
                    Edit Role
                </button>
                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn btn-danger">
                    Delete User
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update User Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=users&action=updateRole">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" id="edit_username" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Current Role</label>
                        <input type="text" id="edit_current_role" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>New Role</label>
                        <select name="role" id="edit_new_role" class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="instructor">Instructor</option>
                            <option value="teaching_assistant">Teaching Assistant</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(id, username, currentRole) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_current_role').value = currentRole.toUpperCase();
    document.getElementById('edit_new_role').value = '';
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.href = 'index.php?page=users&action=delete&id=' + id;
    }
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>