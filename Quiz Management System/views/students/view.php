<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                             style="width: 100px; height: 100px; font-size: 36px;">
                            <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Student ID</th>
                            <td><?php echo $student['student_id']; ?></td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td><?php echo $student['full_name']; ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo $student['email']; ?></td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td><?php echo $student['phone'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo $student['address'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th>Course</th>
                            <td><?php echo $student['course']; ?></td>
                        </tr>
                        <tr>
                            <th>Year Level</th>
                            <td><?php echo $student['year_level']; ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-<?php echo $student['status'] == 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($student['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Registered On</th>
                            <td><?php echo date('F d, Y', strtotime($student['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Enrolled Courses</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Enrollment Date</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Get enrollments for this student
                                $stmt = $conn->prepare("SELECT e.*, c.course_code, c.course_name FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ?");
                                $stmt->bind_param("i", $student['id']);
                                $stmt->execute();
                                $enrollments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                ?>
                                <?php foreach($enrollments as $enrollment): ?>
                                <tr>
                                    <td><?php echo $enrollment['course_code']; ?></td>
                                    <td><?php echo $enrollment['course_name']; ?></td>
                                    <td><?php echo $enrollment['enrollment_date']; ?></td>
                                    <td><?php echo $enrollment['grade'] ?? 'Not graded'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $enrollment['status'] == 'enrolled' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($enrollment['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <a href="index.php?page=students" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
            <?php if (hasRole('admin')): ?>
            <a href="index.php?page=students&action=edit&id=<?php echo $student['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Student
            </a>
            <?php endif; ?>
            <button onclick="printStudentReport()" class="btn btn-info">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
</div>

<script>
function printStudentReport() {
    var printContents = document.querySelector('.card').outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>