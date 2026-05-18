<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Announcement Management</h4>
            <a href="index.php?page=announcements&action=create" class="btn btn-primary float-end">
                <i class="fas fa-plus"></i> New Announcement
            </a>
        </div>
        <div class="card-body">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Announcements</h5>
                            <h2><?php echo $stats['total']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Active</h5>
                            <h2><?php echo $stats['active']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h5>Expired</h5>
                            <h2><?php echo $stats['expired']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Total Views</h5>
                            <h2><?php echo $stats['total_views']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filterType" class="form-control">
                        <option value="all">All Types</option>
                        <?php foreach($announcementTypes as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo ($_GET['type'] ?? '') == $type ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterTargetRole" class="form-control">
                        <option value="all">All Targets</option>
                        <?php foreach($targetRoles as $role): ?>
                            <option value="<?php echo $role; ?>" <?php echo ($_GET['target_role'] ?? '') == $role ? 'selected' : ''; ?>>
                                <?php echo ucfirst($role); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterStatus" class="form-control">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" id="searchAnnouncement" class="form-control" placeholder="Search announcements...">
                        <button class="btn btn-primary" onclick="searchAnnouncements()">Search</button>
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
            
            <!-- Announcements Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Target</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="announcementTableBody">
                        <?php foreach($announcements as $announcement): ?>
                        <tr>
                            <td><?php echo $announcement['id']; ?></td>
                            <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo ucfirst($announcement['announcement_type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo ucfirst($announcement['target_role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $priority_class = '';
                                if ($announcement['priority'] == 'urgent') $priority_class = 'bg-danger';
                                elseif ($announcement['priority'] == 'high') $priority_class = 'bg-warning';
                                elseif ($announcement['priority'] == 'medium') $priority_class = 'bg-info';
                                else $priority_class = 'bg-secondary';
                                ?>
                                <span class="badge <?php echo $priority_class; ?>">
                                    <?php echo ucfirst($announcement['priority']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $status_class = $announcement['status'] == 'active' ? 'success' : ($announcement['status'] == 'inactive' ? 'secondary' : 'danger');
                                ?>
                                <span class="badge bg-<?php echo $status_class; ?>">
                                    <?php echo ucfirst($announcement['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $announcement['view_count']; ?></td>
                            <td><?php echo $announcement['expires_at'] ? date('Y-m-d', strtotime($announcement['expires_at'])) : 'Never'; ?></td>
                            <td>
                                <a href="index.php?page=announcements&action=view&id=<?php echo $announcement['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="index.php?page=announcements&action=edit&id=<?php echo $announcement['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button onclick="updateStatus(<?php echo $announcement['id']; ?>, '<?php echo $announcement['status']; ?>')" class="btn btn-secondary btn-sm">Change Status</button>
                                <a href="index.php?page=announcements&action=delete&id=<?php echo $announcement['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Announcement Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="status_announcement_id">
                <div class="mb-3">
                    <label>Select New Status</label>
                    <select id="new_status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmStatusChange()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    var type = $('#filterType').val();
    var target_role = $('#filterTargetRole').val();
    var status = $('#filterStatus').val();
    window.location.href = 'index.php?page=announcements&type=' + type + '&target_role=' + target_role + '&status=' + status;
}

function resetFilters() {
    window.location.href = 'index.php?page=announcements';
}

function searchAnnouncements() {
    var keyword = $('#searchAnnouncement').val().trim();
    if (keyword.length >= 2) {
        $.ajax({
            url: 'index.php?page=announcements&action=search',
            type: 'POST',
            data: {search: keyword},
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    displayAnnouncements(data.announcements);
                }
            }
        });
    } else if (keyword.length == 0) {
        location.reload();
    }
}

function updateStatus(id, currentStatus) {
    $('#status_announcement_id').val(id);
    $('#new_status').val(currentStatus);
    $('#statusModal').modal('show');
}

function confirmStatusChange() {
    var id = $('#status_announcement_id').val();
    var newStatus = $('#new_status').val();
    
    $.ajax({
        url: 'index.php?page=announcements&action=updateStatus',
        type: 'POST',
        data: {announcement_id: id, status: newStatus},
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                alert(response.message);
                location.reload();
            } else {
                alert(response.message);
            }
            $('#statusModal').modal('hide');
        }
    });
}

$('#searchAnnouncement').on('keypress', function(e) {
    if (e.which == 13) searchAnnouncements();
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>