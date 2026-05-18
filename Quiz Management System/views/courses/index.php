<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Course Management</h4>
            <a href="index.php?page=courses&action=create" class="btn btn-primary float-end">
                Add New Course
            </a>
        </div>
        <div class="card-body">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Courses</h5>
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
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h5>Archived</h5>
                            <h2><?php echo $stats['archived']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filterSubject" class="form-control">
                        <option value="all">All Subjects</option>
                        <?php foreach($subjects as $subject_option): ?>
                            <option value="<?php echo $subject_option; ?>" <?php echo ($subject == $subject_option) ? 'selected' : ''; ?>>
                                <?php echo $subject_option; ?>
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
                    <select id="filterInstructor" class="form-control">
                        <option value="all">All Instructors</option>
                        <?php foreach($instructors as $instructor): ?>
                            <option value="<?php echo $instructor['id']; ?>" <?php echo ($instructor_id == $instructor['id']) ? 'selected' : ''; ?>>
                                <?php echo $instructor['full_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" id="searchCourse" class="form-control" placeholder="Search by course code or name...">
                        <button class="btn btn-primary" onclick="searchCourses()">Search</button>
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
            
            <!-- Courses Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Subject</th>
                            <th>Instructor</th>
                            <th>Credits</th>
                            <th>Status</th>
                            <th>Enrollments</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="courseTableBody">
                        <?php foreach($courses as $course): ?>
                        <tr>
                            <td><?php echo $course['id']; ?></td>
                            <td><?php echo $course['course_code']; ?></td>
                            <td><?php echo $course['course_name']; ?></td>
                            <td><?php echo $course['subject']; ?></td>
                            <td><?php echo $course['instructor_name'] ?? 'Not Assigned'; ?></td>
                            <td><?php echo $course['credits']; ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                if ($course['status'] == 'active') {
                                    $status_class = 'bg-success';
                                    $status_text = 'Active';
                                } elseif ($course['status'] == 'draft') {
                                    $status_class = 'bg-warning';
                                    $status_text = 'Draft';
                                } else {
                                    $status_class = 'bg-secondary';
                                    $status_text = 'Archived';
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td><?php echo $course['enrollment_count'] ?? 0; ?></td>
                            <td>
                                <a href="index.php?page=courses&action=view&id=<?php echo $course['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="index.php?page=courses&action=edit&id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="index.php?page=courses&action=delete&id=<?php echo $course['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No courses found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    var subject = $('#filterSubject').val();
    var status = $('#filterStatus').val();
    var instructor = $('#filterInstructor').val();
    window.location.href = 'index.php?page=courses&subject=' + subject + '&status=' + status + '&instructor=' + instructor;
}

function resetFilters() {
    window.location.href = 'index.php?page=courses';
}

function searchCourses() {
    var keyword = $('#searchCourse').val().trim();
    
    if (keyword.length == 0) {
        showMessage('Please enter a search keyword', 'warning');
        return;
    }
    
    if (keyword.length >= 2) {
        showLoading();
        $.ajax({
            url: 'index.php?page=courses&action=search',
            type: 'POST',
            data: {search: keyword},
            dataType: 'json',
            success: function(data) {
                hideLoading();
                if (data.status == 'success') {
                    displayCourses(data.courses);
                    showMessage('Found ' + data.courses.length + ' course(s)', 'info');
                } else {
                    showMessage(data.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                showMessage('Search failed: ' + error, 'error');
            }
        });
    } else {
        showMessage('Please enter at least 2 characters', 'warning');
    }
}

function displayCourses(courses) {
    var html = '';
    if (courses.length > 0) {
        $.each(courses, function(index, course) {
            var statusClass = '';
            var statusText = '';
            if (course.status == 'active') {
                statusClass = 'bg-success';
                statusText = 'Active';
            } else if (course.status == 'draft') {
                statusClass = 'bg-warning';
                statusText = 'Draft';
            } else {
                statusClass = 'bg-secondary';
                statusText = 'Archived';
            }
            
            html += '<tr>';
            html += '<td>' + course.id + '</td>';
            html += '<td>' + escapeHtml(course.course_code) + '</td>';
            html += '<td>' + escapeHtml(course.course_name) + '</td>';
            html += '<td>' + escapeHtml(course.subject) + '</td>';
            html += '<td>' + escapeHtml(course.instructor_name || 'Not Assigned') + '</td>';
            html += '<td>' + (course.credits || 'N/A') + '</td>';
            html += '<td><span class="badge ' + statusClass + '">' + statusText + '</span></td>';
            html += '<td>0</td>';
            html += '<td>';
            html += '<a href="index.php?page=courses&action=view&id=' + course.id + '" class="btn btn-info btn-sm">View</a> ';
            html += '<a href="index.php?page=courses&action=edit&id=' + course.id + '" class="btn btn-warning btn-sm">Edit</a> ';
            html += '<a href="index.php?page=courses&action=delete&id=' + course.id + '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</a>';
            html += '</td>';
            html += '</tr>';
        });
    } else {
        html = '<tr><td colspan="9" class="text-center">No courses found</td></tr>';
    }
    $('#courseTableBody').html(html);
}

function showMessage(message, type) {
    var alertClass = '';
    if (type == 'success') alertClass = 'alert-success';
    else if (type == 'error') alertClass = 'alert-danger';
    else if (type == 'warning') alertClass = 'alert-warning';
    else alertClass = 'alert-info';
    
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; min-width: 300px;">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';
    
    $('body').append(alertHtml);
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 3000);
}

function showLoading() {
    var loadingHtml = '<div id="loadingSpinner" class="position-fixed top-50 start-50 translate-middle bg-dark text-white p-3 rounded" style="z-index: 9999;">' +
        '<div class="spinner-border text-light" role="status">' +
        '<span class="visually-hidden">Loading...</span>' +
        '</div>' +
        '<div class="mt-2">Searching...</div>' +
        '</div>';
    $('body').append(loadingHtml);
}

function hideLoading() {
    $('#loadingSpinner').remove();
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// Enter key press search
$('#searchCourse').on('keypress', function(e) {
    if (e.which == 13) {
        searchCourses();
    }
});
</script>

<style>
.btn-sm {
    margin: 2px;
}
.badge {
    font-size: 12px;
    padding: 5px 10px;
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>