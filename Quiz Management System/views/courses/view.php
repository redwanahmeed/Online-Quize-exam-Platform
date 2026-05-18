<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Course Details</h4>
            <a href="index.php?page=courses" class="btn btn-secondary float-end">Back to Courses</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 35%;">Course ID</th>
                            <td><?php echo $course['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Course Code</th>
                            <td><?php echo $course['course_code']; ?></td>
                        </tr>
                        <tr>
                            <th>Course Name</th>
                            <td><?php echo $course['course_name']; ?></td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td><?php echo $course['subject']; ?></td>
                        </tr>
                        <tr>
                            <th>Credits</th>
                            <td><?php echo $course['credits']; ?></td>
                        </tr>
                        <tr>
                            <th>Instructor</th>
                            <td><?php echo $course['instructor_name'] ?? 'Not Assigned'; ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php
                                $status_badge = [
                                    'active' => 'success',
                                    'draft' => 'warning',
                                    'archived' => 'secondary'
                                ];
                                $badge = $status_badge[$course['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badge; ?>">
                                    <?php echo ucfirst($course['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Enrollments</th>
                            <td><?php echo $enrollment_count; ?></td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td><?php echo date('F d, Y', strtotime($course['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Description</h5>
                        </div>
                        <div class="card-body">
                            <p><?php echo nl2br($course['description'] ?: 'No description provided.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <a href="index.php?page=courses&action=edit&id=<?php echo $course['id']; ?>" class="btn btn-warning">Edit Course</a>
                    <a href="index.php?page=courses&action=delete&id=<?php echo $course['id']; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Are you sure?')">Delete Course</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>