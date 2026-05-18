<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-edit"></i> Edit Announcement</h4>
                    <a href="index.php?page=announcements" class="btn btn-secondary float-end">Back to Announcements</a>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($announcement) && $announcement): ?>
                    <form method="POST" action="index.php?page=announcements&action=edit&id=<?php echo $announcement['id']; ?>">
                        <div class="mb-3">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Content <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Announcement Type</label>
                                <select name="announcement_type" class="form-control">
                                    <option value="general" <?php echo ($announcement['announcement_type'] == 'general') ? 'selected' : ''; ?>>General</option>
                                    <option value="maintenance" <?php echo ($announcement['announcement_type'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                                    <option value="exam" <?php echo ($announcement['announcement_type'] == 'exam') ? 'selected' : ''; ?>>Exam</option>
                                    <option value="update" <?php echo ($announcement['announcement_type'] == 'update') ? 'selected' : ''; ?>>Update</option>
                                    <option value="emergency" <?php echo ($announcement['announcement_type'] == 'emergency') ? 'selected' : ''; ?>>Emergency</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label>Target Audience</label>
                                <select name="target_role" class="form-control">
                                    <option value="all" <?php echo ($announcement['target_role'] == 'all') ? 'selected' : ''; ?>>All Users</option>
                                    <option value="admin" <?php echo ($announcement['target_role'] == 'admin') ? 'selected' : ''; ?>>Admin Only</option>
                                    <option value="instructor" <?php echo ($announcement['target_role'] == 'instructor') ? 'selected' : ''; ?>>Instructor Only</option>
                                    <option value="teaching_assistant" <?php echo ($announcement['target_role'] == 'teaching_assistant') ? 'selected' : ''; ?>>Teaching Assistant Only</option>
                                    <option value="student" <?php echo ($announcement['target_role'] == 'student') ? 'selected' : ''; ?>>Student Only</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="low" <?php echo ($announcement['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo ($announcement['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo ($announcement['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                                    <option value="urgent" <?php echo ($announcement['priority'] == 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" <?php echo ($announcement['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($announcement['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="expired" <?php echo ($announcement['status'] == 'expired') ? 'selected' : ''; ?>>Expired</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Expiry Date (Optional)</label>
                            <input type="date" name="expires_at" class="form-control" value="<?php echo $announcement['expires_at']; ?>">
                            <small>Leave empty if no expiry date</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Announcement</button>
                        <a href="index.php?page=announcements" class="btn btn-secondary">Cancel</a>
                    </form>
                    <?php else: ?>
                        <div class="alert alert-danger">Announcement not found!</div>
                        <a href="index.php?page=announcements" class="btn btn-primary">Back to Announcements</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>