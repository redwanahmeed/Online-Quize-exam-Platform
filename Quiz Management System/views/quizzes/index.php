<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Quiz Management</h4>
            <a href="index.php?page=quizzes&action=create" class="btn btn-primary float-end">
                Add New Quiz
            </a>
        </div>
        <div class="card-body">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Quizzes</h5>
                            <h2><?php echo $stats['total']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Active</h5>
                            <h2><?php echo $stats['active']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Draft</h5>
                            <h2><?php echo $stats['draft']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Total Attempts</h5>
                            <h2><?php echo $stats['total_attempts']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filterCourse" class="form-control">
                        <option value="all">All Courses</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo ($course_id == $course['id']) ? 'selected' : ''; ?>>
                                <?php echo $course['course_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterStatus" class="form-control">
                        <option value="all">All Status</option>
                        <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="draft" <?php echo ($status == 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="archived" <?php echo ($status == 'archived') ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterType" class="form-control">
                        <option value="all">All Types</option>
                        <option value="exam" <?php echo ($quiz_type == 'exam') ? 'selected' : ''; ?>>Exam</option>
                        <option value="assignment" <?php echo ($quiz_type == 'assignment') ? 'selected' : ''; ?>>Assignment</option>
                        <option value="test" <?php echo ($quiz_type == 'test') ? 'selected' : ''; ?>>Test</option>
                        <option value="practice" <?php echo ($quiz_type == 'practice') ? 'selected' : ''; ?>>Practice</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" id="searchQuiz" class="form-control" placeholder="Search quizzes...">
                        <button class="btn btn-primary" onclick="searchQuizzes()">Search</button>
                    </div>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <button class="btn btn-sm btn-outline-primary" onclick="applyFilters()">Apply Filters</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">Reset Filters</button>
                </div>
            </div>
            
            <!-- Quizzes Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Quiz Title</th>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Total Marks</th>
                            <th>Passing Marks</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Attempts</th>
                            <th>Avg Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="quizTableBody">
                        <?php foreach($quizzes as $quiz): ?>
                        <tr>
                            <td><?php echo $quiz['id']; ?></td>
                            <td><?php echo $quiz['quiz_title']; ?></td>
                            <td><?php echo $quiz['course_name']; ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo ucfirst($quiz['quiz_type']); ?>
                                </span>
                            </td>
                            <td><?php echo $quiz['total_marks']; ?></td>
                            <td><?php echo $quiz['passing_marks']; ?></td>
                            <td><?php echo $quiz['duration_minutes']; ?> min</td>
                            <td>
                                <?php
                                $status_class = '';
                                if ($quiz['status'] == 'active') {
                                    $status_class = 'bg-success';
                                } elseif ($quiz['status'] == 'draft') {
                                    $status_class = 'bg-warning';
                                } else {
                                    $status_class = 'bg-secondary';
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($quiz['status']); ?></span>
                            </td>
                            <td><?php echo $quiz['total_attempts'] ?? 0; ?></td>
                            <td>
                                <?php 
                                $avg = $quiz['avg_percentage'] ?? 0;
                                $avg_class = $avg >= 70 ? 'success' : ($avg >= 50 ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-<?php echo $avg_class; ?>">
                                    <?php echo number_format($avg, 1); ?>%
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=quizzes&action=view&id=<?php echo $quiz['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="index.php?page=quizzes&action=edit&id=<?php echo $quiz['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button onclick="updateQuizStatus(<?php echo $quiz['id']; ?>, '<?php echo $quiz['status']; ?>')" class="btn btn-secondary btn-sm">Change Status</button>
                                <a href="index.php?page=quizzes&action=delete&id=<?php echo $quiz['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Change Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Quiz Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="status_quiz_id">
                <div class="mb-3">
                    <label>Select New Status</label>
                    <select id="new_status" class="form-control">
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmStatusChange()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    var course = $('#filterCourse').val();
    var status = $('#filterStatus').val();
    var type = $('#filterType').val();
    window.location.href = 'index.php?page=quizzes&course=' + course + '&status=' + status + '&type=' + type;
}

function resetFilters() {
    window.location.href = 'index.php?page=quizzes';
}

function searchQuizzes() {
    var keyword = $('#searchQuiz').val().trim();
    if (keyword.length >= 2) {
        $.ajax({
            url: 'index.php?page=quizzes&action=search',
            type: 'POST',
            data: {search: keyword},
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    displayQuizzes(data.quizzes);
                    showMessage('Found ' + data.quizzes.length + ' quiz(zes)', 'info');
                }
            }
        });
    } else if (keyword.length == 0) {
        location.reload();
    } else {
        showMessage('Enter at least 2 characters', 'warning');
    }
}

function updateQuizStatus(id, currentStatus) {
    $('#status_quiz_id').val(id);
    $('#new_status').val(currentStatus);
    $('#statusModal').modal('show');
}

function confirmStatusChange() {
    var id = $('#status_quiz_id').val();
    var newStatus = $('#new_status').val();
    
    $.ajax({
        url: 'index.php?page=quizzes&action=updateStatus',
        type: 'POST',
        data: {quiz_id: id, status: newStatus},
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                showMessage(response.message, 'success');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                showMessage(response.message, 'error');
            }
            $('#statusModal').modal('hide');
        }
    });
}

function showMessage(message, type) {
    var alertClass = type == 'success' ? 'alert-success' : (type == 'error' ? 'alert-danger' : 'alert-info');
    var alertHtml = '<div class="alert ' + alertClass + ' position-fixed top-0 end-0 m-3" style="z-index: 9999;">' + message + '</div>';
    $('body').append(alertHtml);
    setTimeout(function() { $('.alert').fadeOut('slow', function() { $(this).remove(); }); }, 3000);
}

$('#searchQuiz').on('keypress', function(e) {
    if (e.which == 13) searchQuizzes();
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>