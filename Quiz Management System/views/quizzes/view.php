<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Quiz Details: <?php echo $quiz['quiz_title']; ?></h4>
            <a href="index.php?page=quizzes" class="btn btn-secondary float-end">Back to Quizzes</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr><th>Course</th><td><?php echo $quiz['course_name']; ?> (<?php echo $quiz['course_code']; ?>)</td></tr>
                        <tr><th>Quiz Type</th><td><?php echo ucfirst($quiz['quiz_type']); ?></td></tr>
                        <tr><th>Total Marks</th><td><?php echo $quiz['total_marks']; ?></td></tr>
                        <tr><th>Passing Marks</th><td><?php echo $quiz['passing_marks']; ?></td></tr>
                        <tr><th>Duration</th><td><?php echo $quiz['duration_minutes']; ?> minutes</td></tr>
                        <tr><th>Status</th>
                            <td>
                                <?php
                                $status_class = $quiz['status'] == 'active' ? 'success' : ($quiz['status'] == 'draft' ? 'warning' : 'secondary');
                                ?>
                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($quiz['status']); ?></span>
                            </td>
                        </tr>
                        <tr><th>Created At</th><td><?php echo date('F d, Y', strtotime($quiz['created_at'])); ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Description</h5>
                        </div>
                        <div class="card-body">
                            <p><?php echo nl2br($quiz['description'] ?: 'No description provided.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5 class="mt-4">Quiz Attempts</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Attempt Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($attempts as $attempt): ?>
                        <tr>
                            <td><?php echo $attempt['student_id']; ?></td>
                            <td><?php echo $attempt['student_name']; ?></td>
                            <td><?php echo $attempt['obtained_marks']; ?>/<?php echo $attempt['score']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $attempt['percentage'] >= 70 ? 'success' : ($attempt['percentage'] >= 50 ? 'warning' : 'danger'); ?>">
                                    <?php echo number_format($attempt['percentage'], 1); ?>%
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $attempt['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($attempt['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('F d, Y', strtotime($attempt['attempt_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($attempts)): ?>
                        <tr><td colspan="6" class="text-center">No attempts yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>