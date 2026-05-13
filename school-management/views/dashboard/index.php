<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <h2><?php echo $studentStats['total']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Active Students</h5>
                    <h2><?php echo $studentStats['active']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <h2><?php echo count($courses); ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Students</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Course</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent = array_slice($students, 0, 5);
                            foreach($recent as $student): 
                            ?>
                            <tr>
                                <td><?php echo $student['student_id']; ?></td>
                                <td><?php echo $student['full_name']; ?></td>
                                <td><?php echo $student['course']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Available Courses</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach($courses as $course): ?>
                        <li class="list-group-item">
                            <strong><?php echo $course['course_code']; ?></strong> - <?php echo $course['course_name']; ?>
                            <span class="badge bg-secondary float-end"><?php echo $course['credits']; ?> Credits</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>