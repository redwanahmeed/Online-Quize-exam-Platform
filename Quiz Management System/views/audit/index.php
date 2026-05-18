<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-history"></i> Audit Log</h4>
            <button class="btn btn-danger btn-sm float-end" onclick="clearLogs()">
                <i class="fas fa-trash"></i> Clear Old Logs
            </button>
        </div>
        <div class="card-body">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Actions</h5>
                            <h2><?php echo $stats['total']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Today's Activity</h5>
                            <h2><?php echo $stats['today']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Weekly Activity</h5>
                            <div class="row">
                                <?php if (!empty($stats['weekly'])): ?>
                                    <?php foreach($stats['weekly'] as $day): ?>
                                    <div class="col text-center">
                                        <small><?php echo date('d M', strtotime($day['date'])); ?></small>
                                        <h5><?php echo $day['count']; ?></h5>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col text-center">No data available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-2">
                    <select id="filterUser" class="form-control">
                        <option value="">All Users</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo ($_GET['user_id'] ?? '') == $user['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['username']); ?> (<?php echo $user['role']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterAction" class="form-control">
                        <option value="">All Actions</option>
                        <?php foreach($actionTypes as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo ($_GET['action_type'] ?? '') == $type ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterTarget" class="form-control">
                        <option value="">All Targets</option>
                        <?php foreach($targetTypes as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo ($_GET['target_type'] ?? '') == $type ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="filterDateFrom" class="form-control" placeholder="Date From" value="<?php echo $_GET['date_from'] ?? ''; ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" id="filterDateTo" class="form-control" placeholder="Date To" value="<?php echo $_GET['date_to'] ?? ''; ?>">
                </div>
                <div class="col-md-2">
                    <select id="filterLimit" class="form-control">
                        <option value="50" <?php echo ($_GET['limit'] ?? 100) == 50 ? 'selected' : ''; ?>>Last 50</option>
                        <option value="100" <?php echo ($_GET['limit'] ?? 100) == 100 ? 'selected' : ''; ?>>Last 100</option>
                        <option value="200" <?php echo ($_GET['limit'] ?? 100) == 200 ? 'selected' : ''; ?>>Last 200</option>
                        <option value="500" <?php echo ($_GET['limit'] ?? 100) == 500 ? 'selected' : ''; ?>>Last 500</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" id="searchLog" class="form-control" placeholder="Search by action, user, or details..." value="<?php echo $_GET['search'] ?? ''; ?>">
                        <button class="btn btn-primary" onclick="applyFilters()">Search</button>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-secondary" onclick="resetFilters()">Reset Filters</button>
                </div>
            </div>
            
            <!-- Logs Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action Type</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>Target Name</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>Date & Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php foreach($logs as $log): ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                                    <br><small class="text-muted">(<?php echo $log['user_role']; ?>)</small>
                                </td>
                                <td>
                                    <?php
                                    $action_class = '';
                                    $action_icon = '';
                                    if ($log['action_type'] == 'create') {
                                        $action_class = 'success';
                                        $action_icon = 'fa-plus';
                                    } elseif ($log['action_type'] == 'update') {
                                        $action_class = 'info';
                                        $action_icon = 'fa-edit';
                                    } elseif ($log['action_type'] == 'delete') {
                                        $action_class = 'danger';
                                        $action_icon = 'fa-trash';
                                    } elseif ($log['action_type'] == 'role_change') {
                                        $action_class = 'warning';
                                        $action_icon = 'fa-exchange-alt';
                                    } elseif ($log['action_type'] == 'approve') {
                                        $action_class = 'success';
                                        $action_icon = 'fa-check-circle';
                                    } elseif ($log['action_type'] == 'login') {
                                        $action_class = 'primary';
                                        $action_icon = 'fa-sign-in-alt';
                                    } elseif ($log['action_type'] == 'logout') {
                                        $action_class = 'secondary';
                                        $action_icon = 'fa-sign-out-alt';
                                    } else {
                                        $action_class = 'secondary';
                                        $action_icon = 'fa-info-circle';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $action_class; ?>">
                                        <i class="fas <?php echo $action_icon; ?>"></i>
                                        <?php echo ucfirst(str_replace('_', ' ', $log['action_type'])); ?>
                                    </span>
                                </td>
                                <td><small><?php echo htmlspecialchars(substr($log['action'], 0, 60)); ?></small></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo ucfirst($log['target_type'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td><small><?php echo htmlspecialchars($log['target_name'] ?? 'N/A'); ?></small></td>
                                <td><small><?php echo htmlspecialchars(substr($log['details'] ?? 'N/A', 0, 50)); ?></small></td>
                                <td><small><?php echo $log['ip_address']; ?></small></td>
                                <td><small><?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></small></td>
                                <td>
                                    <a href="index.php?page=audit&action=view&id=<?php echo $log['id']; ?>" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No audit logs found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    var user_id = $('#filterUser').val();
    var action_type = $('#filterAction').val();
    var target_type = $('#filterTarget').val();
    var date_from = $('#filterDateFrom').val();
    var date_to = $('#filterDateTo').val();
    var search = $('#searchLog').val();
    var limit = $('#filterLimit').val();
    
    var url = 'index.php?page=audit';
    var params = [];
    
    if (user_id) params.push('user_id=' + user_id);
    if (action_type) params.push('action_type=' + action_type);
    if (target_type) params.push('target_type=' + target_type);
    if (date_from) params.push('date_from=' + date_from);
    if (date_to) params.push('date_to=' + date_to);
    if (search) params.push('search=' + encodeURIComponent(search));
    if (limit) params.push('limit=' + limit);
    
    if (params.length > 0) {
        url += '&' + params.join('&');
    }
    
    window.location.href = url;
}

function resetFilters() {
    window.location.href = 'index.php?page=audit';
}

function clearLogs() {
    if (confirm('Are you sure you want to clear logs older than 90 days? This action cannot be undone!')) {
        window.location.href = 'index.php?page=audit&action=clear&days=90';
    }
}

// Auto apply filters on change
$('#filterUser, #filterAction, #filterTarget, #filterLimit, #filterDateFrom, #filterDateTo').on('change', function() {
    applyFilters();
});

// Enter key search
$('#searchLog').on('keypress', function(e) {
    if (e.which == 13) {
        applyFilters();
    }
});
</script>

<style>
.badge {
    font-size: 11px;
    padding: 5px 8px;
}
.table td {
    vertical-align: middle;
}
.table-responsive {
    overflow-x: auto;
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>