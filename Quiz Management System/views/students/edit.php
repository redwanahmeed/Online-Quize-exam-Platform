<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Student</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Student ID</label>
                                <input type="text" value="<?php echo $student['student_id']; ?>" class="form-control" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Full Name</label>
                                <input type="text" name="full_name" value="<?php echo $student['full_name']; ?>" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo $student['email']; ?>" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone</label>
                                <input type="text" name="phone" value="<?php echo $student['phone']; ?>" class="form-control">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo $student['address']; ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Course</label>
                                <select name="course" class="form-control" required>
                                    <option value="CS101" <?php echo $student['course'] == 'CS101' ? 'selected' : ''; ?>>CS101 - Introduction to Programming</option>
                                    <option value="CS102" <?php echo $student['course'] == 'CS102' ? 'selected' : ''; ?>>CS102 - Database Management</option>
                                    <option value="CS103" <?php echo $student['course'] == 'CS103' ? 'selected' : ''; ?>>CS103 - Web Development</option>
                                    <option value="CS104" <?php echo $student['course'] == 'CS104' ? 'selected' : ''; ?>>CS104 - Software Engineering</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Year Level</label>
                                <select name="year_level" class="form-control" required>
                                    <option value="1" <?php echo $student['year_level'] == 1 ? 'selected' : ''; ?>>1st Year</option>
                                    <option value="2" <?php echo $student['year_level'] == 2 ? 'selected' : ''; ?>>2nd Year</option>
                                    <option value="3" <?php echo $student['year_level'] == 3 ? 'selected' : ''; ?>>3rd Year</option>
                                    <option value="4" <?php echo $student['year_level'] == 4 ? 'selected' : ''; ?>>4th Year</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" <?php echo $student['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $student['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Student</button>
                        <a href="index.php?page=students" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>