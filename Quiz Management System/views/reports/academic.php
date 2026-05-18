<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-chart-line"></i> Student Academic Report</h4>
        </div>
        <div class="card-body">
            <!-- Search Section -->
            <div class="row mb-4">
                <div class="col-md-6 mx-auto">
                    <div class="input-group">
                        <input type="text" id="searchStudent" class="form-control form-control-lg" 
                               placeholder="Search by Student ID or Name..." autocomplete="off">
                        <button class="btn btn-primary btn-lg" onclick="searchStudents()">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <div id="searchResults" class="mt-2" style="display: none;">
                        <div class="list-group" id="resultsList"></div>
                    </div>
                </div>
            </div>
            
            <?php if (isset($student) && $student): ?>
                <!-- Student Profile Section -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Student ID:</strong><br>
                                        <h4><?php echo $student['student_id']; ?></h4>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Full Name:</strong><br>
                                        <h4><?php echo htmlspecialchars($student['full_name']); ?></h4>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Course:</strong><br>
                                        <h4><?php echo htmlspecialchars($student['course']); ?></h4>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Year Level:</strong><br>
                                        <h4><?php echo $student['year_level']; ?></h4>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <strong>Email:</strong><br>
                                        <?php echo htmlspecialchars($student['email']); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Phone:</strong><br>
                                        <?php echo $student['phone'] ?? 'N/A'; ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Status:</strong><br>
                                        <?php if ($student['status'] == 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Enrolled Since:</strong><br>
                                        <?php echo date('F d, Y', strtotime($student['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Performance Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center">
                                <h5>Overall GPA</h5>
                                <h2><?php echo number_format($performance['gpa'], 2); ?></h2>
                                <small>Scale: 4.00</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h5>Courses Completed</h5>
                                <h2><?php echo $performance['completed_courses']; ?> / <?php echo $performance['total_courses']; ?></h2>
                                <small>Total: <?php echo $performance['total_courses']; ?> courses</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h5>Total Quiz Attempts</h5>
                                <h2><?php echo $quiz_stats['total_attempts']; ?></h2>
                                <small>Completed: <?php echo $quiz_stats['completed_attempts']; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <h5>Average Quiz Score</h5>
                                <h2><?php echo number_format($quiz_stats['average_score'], 1); ?>%</h2>
                                <small>Passing Rate: <?php echo $quiz_stats['passing_rate'] ?? 0; ?>%</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Course Details Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-book"></i> Course Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Credits</th>
                                        <th>Enrollment Date</th>
                                        <th>Grade</th>
                                        <th>Grade Point</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($courses)): ?>
                                        <?php foreach($courses as $course): ?>
                                        <tr>
                                            <td><?php echo $course['course_code']; ?></td>
                                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                            <td><?php echo $course['credits']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($course['enrollment_date'])); ?></td>
                                            <td>
                                                <?php if ($course['grade']): ?>
                                                    <span class="badge bg-info"><?php echo $course['grade']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Not Graded</span>
                                                <?php endif; ?>
                                            </td
                                            <td>
                                                <?php 
                                                if ($course['grade_point'] > 0) {
                                                    echo number_format($course['grade_point'], 2);
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td
                                            <td>
                                                <?php
                                                $status_class = '';
                                                if ($course['status'] == 'completed') $status_class = 'success';
                                                elseif ($course['status'] == 'enrolled') $status_class = 'primary';
                                                else $status_class = 'danger';
                                                ?>
                                                <span class="badge bg-<?php echo $status_class; ?>">
                                                    <?php echo ucfirst($course['status']); ?>
                                                </span>
                                            </td
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No courses enrolled</td
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                             </table>
                        </div>
                    </div>
                </div>
                
                <!-- Quiz Attempts Table with Avg Score, Attempts, Passing Marks -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-question-circle"></i> Quiz Performance Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Quiz Title</th>
                                        <th>Course</th>
                                        <th>Your Score</th>
                                        <th>Total Marks</th>
                                        <th>Percentage</th>
                                        <th>Attempts</th>
                                        <th>Avg Score</th>
                                        <th>Passing Marks</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($quiz_attempts)): ?>
                                        <?php 
                                        // Calculate per quiz statistics
                                        $quiz_summary = [];
                                        foreach($quiz_attempts as $attempt) {
                                            $quiz_id = $attempt['quiz_id'];
                                            if (!isset($quiz_summary[$quiz_id])) {
                                                $quiz_summary[$quiz_id] = [
                                                    'quiz_title' => $attempt['quiz_title'],
                                                    'course_name' => $attempt['course_name'],
                                                    'total_marks' => $attempt['total_marks'],
                                                    'passing_marks' => $attempt['passing_marks'],
                                                    'attempts' => [],
                                                    'best_score' => 0
                                                ];
                                            }
                                            $quiz_summary[$quiz_id]['attempts'][] = $attempt;
                                            $percentage = $attempt['percentage'] ?? 0;
                                            if ($percentage > $quiz_summary[$quiz_id]['best_score']) {
                                                $quiz_summary[$quiz_id]['best_score'] = $percentage;
                                            }
                                        }
                                        
                                        foreach($quiz_attempts as $attempt): 
                                            $quiz_id = $attempt['quiz_id'];
                                            $summary = $quiz_summary[$quiz_id] ?? null;
                                            
                                            // Calculate average score for this quiz
                                            $avg_score = 0;
                                            $attempt_count = 0;
                                            $total_percentage = 0;
                                            if ($summary) {
                                                foreach($summary['attempts'] as $att) {
                                                    if ($att['status'] == 'completed') {
                                                        $attempt_count++;
                                                        $total_percentage += ($att['percentage'] ?? 0);
                                                    }
                                                }
                                                if ($attempt_count > 0) {
                                                    $avg_score = round($total_percentage / $attempt_count, 1);
                                                }
                                            }
                                            
                                            $passing_marks_display = $attempt['passing_marks'] ?? ($attempt['total_marks'] * 0.4);
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($attempt['quiz_title'] ?? 'N/A'); ?></td
                                            <td><?php echo htmlspecialchars($attempt['course_name'] ?? 'N/A'); ?></td
                                            <td>
                                                <strong><?php echo $attempt['obtained_marks'] ?? 0; ?></strong>
                                            </td
                                            <td><?php echo $attempt['total_marks'] ?? 0; ?></td
                                            <td>
                                                <?php 
                                                $percentage = $attempt['percentage'] ?? 0;
                                                $percent_class = $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?php echo $percent_class; ?>" style="font-size: 14px;">
                                                    <?php echo number_format($percentage, 1); ?>%
                                                </span>
                                              </td
                                            <td>
                                                <span class="badge bg-secondary" style="font-size: 14px;">
                                                    <?php echo $attempt_count; ?>x
                                                </span>
                                              </td
                                            <td>
                                                <span class="badge bg-info" style="font-size: 14px;">
                                                    <?php echo number_format($avg_score, 1); ?>%
                                                </span>
                                              </td
                                            <td>
                                                <?php
                                                $pass_class = ($passing_marks_display > 0) ? 'warning' : 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $pass_class; ?>" style="font-size: 14px;">
                                                    <?php echo $passing_marks_display; ?> / <?php echo $attempt['total_marks']; ?>
                                                </span>
                                              </td
                                            <td>
                                                <?php
                                                $attempt_status = $attempt['status'] ?? 'pending';
                                                $status_class = $attempt_status == 'completed' ? 'success' : ($attempt_status == 'failed' ? 'danger' : 'warning');
                                                ?>
                                                <span class="badge bg-<?php echo $status_class; ?>">
                                                    <?php echo ucfirst($attempt_status); ?>
                                                </span>
                                              </td
                                            <td><?php echo date('M d, Y', strtotime($attempt['attempt_date'])); ?></td
                                          </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No quiz attempts found</td
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Quiz Summary Statistics Cards -->
                <?php if (!empty($quiz_attempts)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Quiz Summary Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center border-primary">
                                    <div class="card-body">
                                        <h6>Average Score (All Quizzes)</h6>
                                        <h3 class="text-primary"><?php echo number_format($quiz_stats['average_score'], 1); ?>%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center border-success">
                                    <div class="card-body">
                                        <h6>Highest Score</h6>
                                        <h3 class="text-success"><?php echo number_format($quiz_stats['highest_score'], 1); ?>%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center border-danger">
                                    <div class="card-body">
                                        <h6>Lowest Score</h6>
                                        <h3 class="text-danger"><?php echo number_format($quiz_stats['lowest_score'], 1); ?>%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card text-center border-info">
                                    <div class="card-body">
                                        <h6>Total Attempts</h6>
                                        <h3 class="text-info"><?php echo $quiz_stats['total_attempts']; ?></h3>
                                        <small>Completed: <?php echo $quiz_stats['completed_attempts']; ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card text-center border-warning">
                                    <div class="card-body">
                                        <h6>Passing Rate</h6>
                                        <h3 class="text-warning"><?php echo number_format($quiz_stats['passing_rate'] ?? 0, 1); ?>%</h3>
                                        <small>Passed: <?php echo $quiz_stats['passed_attempts']; ?> / <?php echo $quiz_stats['completed_attempts']; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Quiz Statistics by Type -->
                <?php if (!empty($quiz_stats['by_type'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie"></i> Quiz Performance by Type</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach($quiz_stats['by_type'] as $type => $data): ?>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6><?php echo ucfirst($type); ?></h6>
                                        <h4><?php echo $data['count']; ?></h4>
                                        <small>Attempts</small>
                                        <hr>
                                        <h5><?php echo number_format($data['total'] / $data['count'], 1); ?>%</h5>
                                        <small>Average Score</small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Print Button -->
                <div class="row">
                    <div class="col-12 text-center">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                        <a href="index.php?page=reports" class="btn btn-secondary">
                            <i class="fas fa-search"></i> Search Another Student
                        </a>
                    </div>
                </div>
                
            <?php elseif (isset($_GET['search']) && !empty($_GET['search'])): ?>
                <!-- Search Results Info -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No student selected. Click on a student from search results.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function searchStudents() {
    var keyword = $('#searchStudent').val().trim();
    
    if (keyword.length == 0) {
        $('#searchResults').hide();
        return;
    }
    
    if (keyword.length >= 2) {
        $.ajax({
            url: 'index.php?page=reports&action=search',
            type: 'POST',
            data: {search: keyword},
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success' && data.students.length > 0) {
                    displaySearchResults(data.students);
                } else {
                    $('#resultsList').html('<div class="list-group-item text-danger">No students found</div>');
                    $('#searchResults').show();
                }
            },
            error: function() {
                $('#resultsList').html('<div class="list-group-item text-danger">Search failed</div>');
                $('#searchResults').show();
            }
        });
    }
}

function displaySearchResults(students) {
    var html = '';
    $.each(students, function(index, student) {
        html += '<a href="index.php?page=reports&student_id=' + student.id + '" class="list-group-item list-group-item-action">';
        html += '<strong>' + student.student_id + '</strong> - ' + student.full_name;
        html += '<br><small class="text-muted">' + (student.course || 'N/A') + ' - Year ' + (student.year_level || 'N/A') + '</small>';
        html += '</a>';
    });
    $('#resultsList').html(html);
    $('#searchResults').show();
}

// Live search on keyup
$('#searchStudent').on('keyup', function() {
    searchStudents();
});

// Hide results when clicking outside
$(document).click(function(event) {
    if (!$(event.target).closest('#searchStudent, #searchResults').length) {
        $('#searchResults').hide();
    }
});

// Enter key search
$('#searchStudent').on('keypress', function(e) {
    if (e.which == 13) {
        e.preventDefault();
        searchStudents();
    }
});
</script>

<style>
.list-group-item:hover {
    background-color: #f8f9fa;
}
@media print {
    .sidebar, .navbar-custom, .footer, .card-header .btn, .btn-primary, .btn-secondary, #searchStudent, .input-group, #searchResults {
        display: none !important;
    }
    .content {
        margin: 0 !important;
        padding: 0 !important;
    }
}
.badge {
    font-size: 12px;
    padding: 5px 10px;
}
.table td, .table th {
    vertical-align: middle;
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>