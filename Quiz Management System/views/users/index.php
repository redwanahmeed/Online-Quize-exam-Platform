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
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filterRole" class="form-control">
                        <option value="all">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="instructor">Instructor</option>
                        <option value="teaching_assistant">Teaching Assistant</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterUserStatus" class="form-control">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="searchUser" class="form-control" placeholder="Search by username, name or email...">
                        <button class="btn btn-primary" onclick="searchUsers()">Search</button>
                    </div>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <button class="btn btn-sm btn-outline-primary" onclick="applyFilters()">Apply Filters</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">Reset Filters</button>
                </div>
            </div>
            
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
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
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
                            <td>
                                <?php if (($user['status'] ?? 'active') == 'active'): ?>
                                    <span class="badge bg-success" id="status-badge-<?php echo $user['id']; ?>">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger" id="status-badge-<?php echo $user['id']; ?>">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="index.php?page=users&action=view&id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="index.php?page=users&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <button onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['status'] ?? 'active'; ?>')" 
                                            class="btn btn-<?php echo (($user['status'] ?? 'active') == 'active') ? 'secondary' : 'success'; ?> btn-sm"
                                            id="status-btn-<?php echo $user['id']; ?>">
                                        <?php echo (($user['status'] ?? 'active') == 'active') ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changeRoleModal" 
                                            onclick="setRoleData(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['role']; ?>')">
                                        Change Role
                                    </button>
                                    <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn btn-danger btn-sm">Delete</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Current</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change User Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=users&action=updateRole">
                <input type="hidden" name="user_id" id="role_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" id="role_username" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Current Role</label>
                        <input type="text" id="role_current" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>New Role</label>
                        <select name="role" id="role_new" class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="instructor">Instructor</option>
                            <option value="teaching_assistant">Teaching Assistant</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Role</button>
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
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
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
function setRoleData(id, username, currentRole) {
    document.getElementById('role_user_id').value = id;
    document.getElementById('role_username').value = username;
    document.getElementById('role_current').value = currentRole.toUpperCase();
    document.getElementById('role_new').value = '';
}

function applyFilters() {
    var role = $('#filterRole').val();
    var status = $('#filterUserStatus').val();
    window.location.href = 'index.php?page=users&role=' + role + '&status=' + status;
}

function resetFilters() {
    window.location.href = 'index.php?page=users';
}

function toggleUserStatus(userId, currentStatus) {
    var newStatus = (currentStatus == 'active') ? 'inactive' : 'active';
    var action = (newStatus == 'active') ? 'activate' : 'deactivate';

    if (!confirm('Are you sure you want to ' + action + ' this user?')) {
        return;
    }

    var btn = $('#status-btn-' + userId);
    var badge = $('#status-badge-' + userId);

    var originalText = btn.text();
    btn.text('...').prop('disabled', true);

    $.ajax({
        url: 'index.php?page=users&action=toggleStatus',
        type: 'POST',
        data: {
            user_id: userId,
            current_status: currentStatus
        },
        dataType: 'json',

        success: function(response) {
            if (response.status == 'success') {

                // UPDATE STATUS UI
                if (newStatus == 'active') {
                    badge.removeClass('bg-danger')
                         .addClass('bg-success')
                         .text('Active');

                    btn.removeClass('btn-secondary')
                       .addClass('btn-success')
                       .text('Deactivate');

                } else {
                    badge.removeClass('bg-success')
                         .addClass('bg-danger')
                         .text('Inactive');

                    btn.removeClass('btn-success')
                       .addClass('btn-secondary')
                       .text('Activate');
                }

                // IMPORTANT: update onclick status (no refresh needed)
                btn.attr('onclick',
                    "toggleUserStatus(" + userId + ", '" + newStatus + "')"
                );

                showMessage(response.message, 'success');
            } 
            else {
                showMessage(response.message, 'error');
                btn.text(originalText);
            }
        },

        error: function() {
            showMessage('Server error occurred', 'error');
            btn.text(originalText);
        },

        complete: function() {
            btn.prop('disabled', false);
        }
    });
}

function searchUsers() {
    var keyword = $('#searchUser').val().trim();
    if (keyword.length >= 2) {
        showLoading();
        $.ajax({
            url: 'index.php?page=users&action=search',
            type: 'POST',
            data: {search: keyword},
            dataType: 'json',
            success: function(data) {
                hideLoading();
                if (data.status == 'success') {
                    displayUsers(data.users);
                    showMessage('Found ' + data.users.length + ' user(s)', 'info');
                } else {
                    showMessage(data.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showMessage('Search failed', 'error');
            }
        });
    } else if (keyword.length == 0) {
        location.reload();
    } else {
        showMessage('Enter at least 2 characters', 'warning');
    }
}

function displayUsers(users) {
    var html = '';
    var currentUserId = <?php echo $_SESSION['user_id']; ?>;
    
    $.each(users, function(index, user) {
        var roleBadgeClass = user.role == 'admin' ? 'danger' : (user.role == 'instructor' ? 'primary' : (user.role == 'teaching_assistant' ? 'warning' : 'success'));
        var userStatus = user.status || 'active';
        var statusBadgeClass = userStatus == 'active' ? 'success' : 'danger';
        var statusText = userStatus == 'active' ? 'Active' : 'Inactive';
        var actionBtnClass = userStatus == 'active' ? 'secondary' : 'success';
        var actionBtnText = userStatus == 'active' ? 'Deactivate' : 'Activate';
        
        html += '<tr>';
        html += '<td>' + user.id + '</td>';
        html += '<td>' + escapeHtml(user.username) + '</td>';
        html += '<td>' + escapeHtml(user.full_name) + '</td>';
        html += '<td>' + escapeHtml(user.email) + '</td>';
        html += '<td><span class="badge bg-' + roleBadgeClass + '">' + capitalizeFirst(user.role) + '</span></td>';
        html += '<td><span class="badge bg-' + statusBadgeClass + '" id="status-badge-' + user.id + '">' + statusText + '</span></td>';
        html += '<td>' + user.created_at + '</td>';
        html += '<td>';
        html += '<a href="index.php?page=users&action=view&id=' + user.id + '" class="btn btn-info btn-sm">View</a> ';
        html += '<a href="index.php?page=users&action=edit&id=' + user.id + '" class="btn btn-warning btn-sm">Edit</a> ';
        if (user.id != currentUserId) {
            html += '<button onclick="toggleUserStatus(' + user.id + ', \'' + userStatus + '\')" class="btn btn-' + actionBtnClass + ' btn-sm" id="status-btn-' + user.id + '">' + actionBtnText + '</button> ';
            html += '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changeRoleModal" onclick="setRoleData(' + user.id + ', \'' + escapeHtml(user.username) + '\', \'' + user.role + '\')">Change Role</button> ';
            html += '<button onclick="deleteUser(' + user.id + ')" class="btn btn-danger btn-sm">Delete</button>';
        } else {
            html += '<button class="btn btn-secondary btn-sm" disabled>Current</button>';
        }
        html += '</td>';
        html += '</tr>';
    });
    $('#userTableBody').html(html);
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone!')) {
        window.location.href = 'index.php?page=users&action=delete&id=' + id;
    }
}

function showMessage(message, type) {
    var alertClass = type == 'success' ? 'alert-success' : (type == 'error' ? 'alert-danger' : 'alert-info');
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; min-width: 300px;">' +
        '<i class="fas fa-' + (type == 'success' ? 'check-circle' : (type == 'error' ? 'exclamation-circle' : 'info-circle')) + ' me-2"></i>' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';
    $('body').append(alertHtml);
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() { $(this).remove(); });
    }, 3000);
}

function showLoading() {
    var loadingHtml = '<div id="loadingSpinner" class="position-fixed top-50 start-50 translate-middle bg-dark text-white p-3 rounded" style="z-index: 9999;">' +
        '<div class="spinner-border text-light" role="status"></div>' +
        '<div class="mt-2">Loading...</div>' +
        '</div>';
    $('body').append(loadingHtml);
}

function hideLoading() {
    $('#loadingSpinner').remove();
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function capitalizeFirst(text) {
    if (!text) return '';
    return text.charAt(0).toUpperCase() + text.slice(1).replace(/_/g, ' ');
}

$('#searchUser').on('keypress', function(e) {
    if (e.which == 13) searchUsers();
});

// Auto hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow', function() { $(this).remove(); });
}, 5000);
</script>

<style>
.btn-sm {
    margin: 2px;
}
.badge {
    font-size: 11px;
    padding: 5px 8px;
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>