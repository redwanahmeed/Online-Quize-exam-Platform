<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Course</h4>
                    <a href="index.php?page=courses" class="btn btn-secondary float-end">Back</a>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Course Code <span class="text-danger">*</span></label>
                                <input type="text" name="course_code" class="form-control" value="<?php echo $course['course_code']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Course Name <span class="text-danger">*</span></label>
                                <input type="text" name="course_name" class="form-control" value="<?php echo $course['course_name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Subject <span class="text-danger">*</span></label>
                                <select name="subject" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    <option value="Computer Science" <?php echo $course['subject'] == 'Computer Science' ? 'selected' : ''; ?>>Computer Science</option>
                                    <option value="Software Engineering" <?php echo $course['subject'] == 'Software Engineering' ? 'selected' : ''; ?>>Software Engineering</option>
                                    <option value="Data Science" <?php echo $course['subject'] == 'Data Science' ? 'selected' : ''; ?>>Data Science</option>
                                    <option value="Information Technology" <?php echo $course['subject'] == 'Information Technology' ? 'selected' : ''; ?>>Information Technology</option>
                                    <option value="Business Administration" <?php echo $course['subject'] == 'Business Administration' ? 'selected' : ''; ?>>Business Administration</option>
                                    <option value="Mathematics" <?php echo $course['subject'] == 'Mathematics' ? 'selected' : ''; ?>>Mathematics</option>
                                    <option value="Physics" <?php echo $course['subject'] == 'Physics' ? 'selected' : ''; ?>>Physics</option>
                                    <option value="English" <?php echo $course['subject'] == 'English' ? 'selected' : ''; ?>>English</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Credits <span class="text-danger">*</span></label>
                                <input type="number" name="credits" class="form-control" value="<?php echo $course['credits']; ?>" required min="1" max="6">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo $course['description']; ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Instructor</label>
                                <select name="instructor_id" class="form-control">
                                    <option value="">Select Instructor</option>
                                    <?php foreach($instructors as $instructor): ?>
                                        <option value="<?php echo $instructor['id']; ?>" <?php echo $course['instructor_id'] == $instructor['id'] ? 'selected' : ''; ?>>
                                            <?php echo $instructor['full_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="active" <?php echo $course['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="draft" <?php echo $course['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="archived" <?php echo $course['status'] == 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
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