<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Course</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Course Code</label>
                            <input type="text" name="course_code" value="<?php echo $course['course_code']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Course Name</label>
                            <input type="text" name="course_name" value="<?php echo $course['course_name']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $course['description']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Credits</label>
                            <input type="number" name="credits" value="<?php echo $course['credits']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo $course['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $course['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Course</button>
                        <a href="index.php?page=courses" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>