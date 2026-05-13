<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>User Management</h4>
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addUserModal">
                Add New User
            </button>
        </div>
        <div class="card-body">
            <!-- Search Box -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="searchUser" class="form-control" placeholder="Search by username, name or email...">
                        <button class="btn btn-primary" onclick="searchUsers()">
                            Search
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php if (isset($users) && !empty($users)): ?>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
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
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal" 
                                                onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['role']; ?>')">
                                            Edit Role
                                        </button>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn btn-danger btn-sm" title="Delete User">
                                            Delete
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            Current
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
            <form method="POST" action="index.php?page=users&action=updateRole" id="editRoleForm">
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
                        <label>New Role <span class="text-danger">*</span></label>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=users&action=create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    <div class="mb-3">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="instructor">Instructor</option>
                            <option value="teaching_assistant">Teaching Assistant</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
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

function searchUsers() {
    var keyword = $('#searchUser').val();
    if (keyword.length >= 2) {
        $.ajax({
            url: 'index.php?page=users&action=search',
            type: 'POST',
            data: {search: keyword},
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    displayUsers(data.users);
                }
            }
        });
    } else if (keyword.length == 0) {
        location.reload();
    }
}

function displayUsers(users) {
    var html = '';
    var currentUserId = <?php echo $_SESSION['user_id']; ?>;
    var roleBadges = {
        'admin': 'danger',
        'instructor': 'primary',
        'teaching_assistant': 'warning',
        'student': 'success'
    };
    
    if (users.length > 0) {
        $.each(users, function(index, user) {
            var badgeColor = roleBadges[user.role] || 'secondary';
            var roleName = user.role.charAt(0).toUpperCase() + user.role.slice(1).replace('_', ' ');
            
            html += '<tr>';
            html += '<td>' + user.id + '</td>';
            html += '<td>' + escapeHtml(user.username) + '</td>';
            html += '<td>' + escapeHtml(user.full_name) + '</td>';
            html += '<td>' + escapeHtml(user.email) + '</td>';
            html += '<td><span class="badge bg-' + badgeColor + '">' + roleName + '</span></td>';
            html += '<td>' + user.created_at + '</td>';
            html += '<td>';
            if (user.id != currentUserId) {
                html += '<button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal" onclick="editUser(' + user.id + ', \'' + escapeHtml(user.username) + '\', \'' + user.role + '\')">Edit Role</button> ';
                html += '<button onclick="deleteUser(' + user.id + ')" class="btn btn-danger btn-sm">Delete</button>';
            } else {
                html += '<button class="btn btn-secondary btn-sm" disabled>Current</button>';
            }
            html += '</td>';
            html += '</tr>';
        });
    } else {
        html = '<tr><td colspan="7" class="text-center">No users found</td></tr>';
    }
    $('#userTableBody').html(html);
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

$('#searchUser').on('keyup', function() {
    if ($(this).val().length >= 2) {
        searchUsers();
    } else if ($(this).val().length == 0) {
        location.reload();
    }
});

setTimeout(function() {
    $('.alert').fadeOut('slow', function() {
        $(this).remove();
    });
}, 5000);
</script>

<style>
.badge {
    font-size: 12px;
    padding: 5px 10px;
}
.btn-sm {
    margin: 0 2px;
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>