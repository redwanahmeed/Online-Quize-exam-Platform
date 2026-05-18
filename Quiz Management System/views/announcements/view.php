<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-bullhorn"></i> Announcement Details</h4>
            <a href="index.php?page=announcements" class="btn btn-secondary float-end">Back to Announcements</a>
        </div>
        <div class="card-body">
            <?php if (isset($announcement) && $announcement): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <strong>Type:</strong><br>
                                        <span class="badge bg-info">
                                            <?php echo ucfirst($announcement['announcement_type']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Target Audience:</strong><br>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst($announcement['target_role']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Priority:</strong><br>
                                        <?php
                                        $priority_class = '';
                                        if ($announcement['priority'] == 'urgent') $priority_class = 'danger';
                                        elseif ($announcement['priority'] == 'high') $priority_class = 'warning';
                                        elseif ($announcement['priority'] == 'medium') $priority_class = 'info';
                                        else $priority_class = 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $priority_class; ?>">
                                            <?php echo ucfirst($announcement['priority']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Status:</strong><br>
                                        <?php
                                        $status_class = $announcement['status'] == 'active' ? 'success' : 'danger';
                                        ?>
                                        <span class="badge bg-<?php echo $status_class; ?>">
                                            <?php echo ucfirst($announcement['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <strong>Content:</strong>
                                        <div class="border p-3 mt-2" style="background-color: #f9f9f9; border-radius: 5px;">
                                            <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Created By:</strong><br>
                                        <?php echo htmlspecialchars($announcement['created_by_name'] ?? 'System'); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Created At:</strong><br>
                                        <?php echo date('F d, Y h:i A', strtotime($announcement['created_at'])); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Expires At:</strong><br>
                                        <?php echo $announcement['expires_at'] ? date('F d, Y', strtotime($announcement['expires_at'])) : 'Never'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <a href="index.php?page=announcements&action=edit&id=<?php echo $announcement['id']; ?>" class="btn btn-warning">Edit Announcement</a>
                        <a href="index.php?page=announcements&action=delete&id=<?php echo $announcement['id']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this announcement?')">Delete Announcement</a>
                        <a href="index.php?page=announcements" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Announcement not found!
                </div>
                <a href="index.php?page=announcements" class="btn btn-primary">Back to Announcements</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>