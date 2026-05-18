<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Student Management</h4>
            <?php if (hasRole('admin')): ?>
            <a href="index.php?page=students&action=create" class="btn btn-primary float-end">
                Add New Student
            </a>
            <?php endif; ?>
        </div>

        <div class="card-body">

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h2><?php echo $stats['total'] ?? 0; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Active Students</h5>
                            <h2><?php echo $stats['active'] ?? 0; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Inactive Students</h5>
                            <h2><?php echo $stats['inactive'] ?? 0; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filterCourse" class="form-control">
                        <option value="all">All Courses</option>
                        <?php 
                        $courses_list = array_unique(array_column($students, 'course'));
                        foreach($courses_list as $course_item): 
                            if($course_item):
                        ?>
                            <option value="<?php echo $course_item; ?>"><?php echo $course_item; ?></option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filterYearLevel" class="form-control">
                        <option value="all">All Year Levels</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filterStatus" class="form-control">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" id="searchStudent" class="form-control" placeholder="Search...">
                        <button class="btn btn-primary" onclick="searchStudents()">Search</button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="studentTableBody">
                        <?php foreach($students as $student): ?>
                        <tr>
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo $student['student_id']; ?></td>
                            <td><?php echo $student['full_name']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td><?php echo $student['course']; ?></td>
                            <td><?php echo $student['year_level']; ?></td>

                            <td>
                                <?php if ($student['status'] == 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="index.php?page=students&action=view&id=<?php echo $student['id']; ?>" class="btn btn-info btn-sm">View</a>

                                <?php if (hasRole('admin')): ?>
                                <a href="index.php?page=students&action=edit&id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">Edit</a>

                                <a href="index.php?page=students&action=delete&id=<?php echo $student['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No students found</td>
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
    var course = $('#filterCourse').val();
    var yearLevel = $('#filterYearLevel').val();
    var status = $('#filterStatus').val();
    window.location.href = 'index.php?page=students&course=' + course + '&year=' + yearLevel + '&status=' + status;
}

function resetFilters() {
    window.location.href = 'index.php?page=students';
}

function searchStudents() {
    var keyword = $('#searchStudent').val().trim();

    if (keyword.length < 2) {
        showMessage('Please enter at least 2 characters', 'warning');
        return;
    }

    showLoading();

    $.ajax({
        url: 'index.php?page=students&action=search',
        type: 'POST',
        data: {search: keyword},
        dataType: 'json',
        success: function(data) {
            hideLoading();
            if (data.status == 'success') {
                displayStudents(data.students);
            } else {
                showMessage(data.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showMessage('Search failed', 'error');
        }
    });
}

function displayStudents(students) {
    var html = '';

    if (students.length > 0) {
        $.each(students, function(index, student) {
            html += '<tr>';
            html += '<td>' + student.id + '</td>';
            html += '<td>' + student.student_id + '</td>';
            html += '<td>' + escapeHtml(student.full_name) + '</td>';
            html += '<td>' + escapeHtml(student.email) + '</td>';
            html += '<td>' + escapeHtml(student.course) + '</td>';
            html += '<td>' + student.year_level + '</td>';

            html += '<td>';
            if (student.status == 'active') {
                html += '<span class="badge bg-success">Active</span>';
            } else {
                html += '<span class="badge bg-danger">Inactive</span>';
            }
            html += '</td>';

            html += '<td>';
            html += '<a href="index.php?page=students&action=view&id=' + student.id + '" class="btn btn-info btn-sm">View</a> ';

            <?php if (hasRole('admin')): ?>
            html += '<a href="index.php?page=students&action=edit&id=' + student.id + '" class="btn btn-warning btn-sm">Edit</a> ';
            html += '<a href="index.php?page=students&action=delete&id=' + student.id + '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</a>';
            <?php endif; ?>

            html += '</td>';
            html += '</tr>';
        });
    } else {
        html = '<tr><td colspan="8" class="text-center">No students found</td></tr>';
    }

    $('#studentTableBody').html(html);
}

function showMessage(message, type) {
    var alertClass = type == 'success' ? 'alert-success' : (type == 'error' ? 'alert-danger' : 'alert-info');

    var alertHtml =
        '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index:9999;min-width:300px;">' +
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
    var loadingHtml =
        '<div id="loadingSpinner" class="position-fixed top-50 start-50 translate-middle bg-dark text-white p-3 rounded" style="z-index:9999;">' +
        '<div class="spinner-border text-light"></div>' +
        '<div class="mt-2">Searching...</div>' +
        '</div>';

    $('body').append(loadingHtml);
}

function hideLoading() {
    $('#loadingSpinner').remove();
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, '&amp;')
               .replace(/</g, '&lt;')
               .replace(/>/g, '&gt;');
}

$('#searchStudent').on('keypress', function(e) {
    if (e.which == 13) searchStudents();
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>