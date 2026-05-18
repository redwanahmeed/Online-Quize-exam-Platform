<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
 
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Users</h6>
                            <h2 class="mb-0"><?php echo $userCounts['total_users']; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Active Courses</h6>
                            <h2 class="mb-0"><?php echo $activeCourses; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-book fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Today's Quiz Attempts</h6>
                            <h2 class="mb-0"><?php echo $todayQuizAttempts; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-question-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Pending Approvals</h6>
                            <h2 class="mb-0"><?php echo $pendingRequests; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
 


    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5>User Distribution by Role</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-primary"><?php echo $userCounts['admins']; ?></h3>
                                <small>Admins</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-success"><?php echo $userCounts['instructors']; ?></h3>
                                <small>Instructors</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-warning"><?php echo $userCounts['teaching_assistants']; ?></h3>
                                <small>Teaching Assistants</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h3 class="text-info"><?php echo $userCounts['students']; ?></h3>
                                <small>Students</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5>Platform Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3><?php echo $totalCourses; ?></h3>
                                <small>Total Courses</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h3><?php echo $totalEnrollments; ?></h3>
                                <small>Total Enrollments</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Quiz Attempts</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recentQuizAttempts as $attempt): ?>
                                <tr>
                                    <td><?php echo $attempt['student_name']; ?></td>
                                    <td><?php echo $attempt['quiz_title']; ?></td>
                                    <td><?php echo $attempt['score']; ?>/<?php echo $attempt['total_marks']; ?></td>
                                    <td><?php echo date('d M', strtotime($attempt['attempt_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $attempt['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                            <?php echo $attempt['status']; ?>
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
        
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5>Pending Instructor Approvals</h5>
                </div>
                <div class="card-body">
                    <?php if (count($pendingRequestsDetails) > 0): ?>
                        <?php foreach($pendingRequestsDetails as $request): ?>
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo $request['full_name']; ?></strong><br>
                                    <small><?php echo $request['email']; ?></small><br>
                                    <small class="text-muted">Expertise: <?php echo $request['subject_expertise']; ?></small>
                                </div>
                                <div>
                                    <form method="POST" action="index.php?page=dashboard&action=approveRequest" style="display: inline-block;">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" action="index.php?page=dashboard&action=rejectRequest" style="display: inline-block;">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No pending requests</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>