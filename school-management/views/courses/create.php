<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Add New Course</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
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
                        <button type="submit" class="btn btn-primary">Add Course</button>
                        <a href="index.php?page=courses" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>