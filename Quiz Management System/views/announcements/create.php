<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Create New Announcement</h4>
                    <a href="index.php?page=announcements" class="btn btn-secondary float-end">Back to Announcements</a>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php?page=announcements&action=create">
                        <div class="mb-3">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Content <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Announcement Type</label>
                                <select name="announcement_type" class="form-control">
                                    <option value="general">General</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="exam">Exam</option>
                                    <option value="update">Update</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label>Target Audience</label>
                                <select name="target_role" class="form-control">
                                    <option value="all">All Users</option>
                                    <option value="admin">Admin Only</option>
                                    <option value="instructor">Instructor Only</option>
                                    <option value="teaching_assistant">Teaching Assistant Only</option>
                                    <option value="student">Student Only</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Expiry Date (Optional)</label>
                            <input type="date" name="expires_at" class="form-control">
                            <small class="text-muted">Leave empty if no expiry date</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Publish Announcement</button>
                        <a href="index.php?page=announcements" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>