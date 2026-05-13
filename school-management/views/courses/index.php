<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Course Management</h4>
                    <?php if (hasRole('admin')): ?>
                    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        Add Course
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Description</th>
                                <th>Credits</th>
                                <th>Status</th>
                                <?php if (hasRole('admin')): ?>
                                <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $courseModel = new CourseModel();
                            $courses = $courseModel->getAllCourses();
                            foreach($courses as $course): 
                            ?>
                            <tr>
                                <td><?php echo $course['course_code']; ?></td>
                                <td><?php echo $course['course_name']; ?></td>
                                <td><?php echo $course['description']; ?></td>
                                <td><?php echo $course['credits']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $course['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo $course['status']; ?>
                                    </span>
                                </td>
                                <?php if (hasRole('admin')): ?>
                                <td>
                                    <button onclick="editCourse(<?php echo $course['id']; ?>)" class="btn btn-sm btn-warning">Edit</button>
                                    <button onclick="deleteCourse(<?php echo $course['id']; ?>)" class="btn btn-sm btn-danger">Delete</button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=courses&action=create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Course Code</label>
                        <input type="text" name="course_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Course Name</label>
                        <input type="text" name="course_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Credits</label>
                        <input type="number" name="credits" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCourse(id) {
    window.location.href = 'index.php?page=courses&action=edit&id=' + id;
}

function deleteCourse(id) {
    if(confirm('Are you sure you want to delete this course?')) {
        window.location.href = 'index.php?page=courses&action=delete&id=' + id;
    }
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>