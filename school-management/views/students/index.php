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
            <!-- Search Box -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" id="searchStudent" class="form-control" placeholder="Search by name, ID, email or course...">
                        <button class="btn btn-primary" onclick="searchStudents()">
                            Search
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Student Table -->
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
                            <th>Total Courses</th>
                            <th>GPA</th>
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
                                <span class="badge bg-<?php echo $student['status'] == 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($student['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $student['total_courses'] ?? 0; ?></td>
                            <td>
                                <?php 
                                if (isset($student['gpa']) && $student['gpa'] !== null && $student['gpa'] > 0) {
                                    echo number_format($student['gpa'], 2);
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="index.php?page=students&action=view&id=<?php echo $student['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <?php if (hasRole('admin')): ?>
                                <a href="index.php?page=students&action=edit&id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="index.php?page=students&action=delete&id=<?php echo $student['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="10" class="text-center">No students found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function searchStudents() {
    var keyword = $('#searchStudent').val();
    if (keyword.length >= 2) {
        $.ajax({
            url: 'index.php?page=students&action=search',
            type: 'POST',
            data: {search: keyword},
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    displayStudents(data.students);
                }
            }
        });
    } else if (keyword.length == 0) {
        location.reload();
    }
}

function displayStudents(students) {
    var html = '';
    $.each(students, function(index, student) {
        html += '<tr>';
        html += '<td>' + student.id + '</td>';
        html += '<td>' + student.student_id + '</td>';
        html += '<td>' + student.full_name + '</td>';
        html += '<td>' + student.email + '</td>';
        html += '<td>' + student.course + '</td>';
        html += '<td>' + student.year_level + '</td>';
        html += '<td><span class="badge bg-' + (student.status == 'active' ? 'success' : 'danger') + '">' + student.status + '</span></td>';
        html += '<td>-</td>';
        html += '<td>-</td>';
        html += '<td>';
        html += '<a href="index.php?page=students&action=view&id=' + student.id + '" class="btn btn-info btn-sm">View</a> ';
        <?php if (hasRole('admin')): ?>
        html += '<a href="index.php?page=students&action=edit&id=' + student.id + '" class="btn btn-warning btn-sm">Edit</a> ';
        html += '<a href="index.php?page=students&action=delete&id=' + student.id + '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</a>';
        <?php endif; ?>
        html += '</td>';
        html += '</tr>';
    });
    $('#studentTableBody').html(html);
}

$('#searchStudent').on('keyup', function() {
    if ($(this).val().length >= 2) {
        searchStudents();
    } else if ($(this).val().length == 0) {
        location.reload();
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>