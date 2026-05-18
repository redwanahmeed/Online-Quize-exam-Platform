<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Student Reports</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Generate Student Report</h5>
                            <button onclick="window.print()" class="btn btn-primary">Print Report</button>
                            <button onclick="generateReport()" class="btn btn-success">Generate PDF Report</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="reportContent" class="mt-4">
                <div class="report-container p-4">
                    <div class="text-center mb-4">
                        <h2><?php echo SITE_NAME; ?></h2>
                        <h4>Student Information Report</h4>
                        <p>Generated on: <?php echo date('F d, Y'); ?></p>
                    </div>
                    
                    <table class="table table-bordered" id="reportTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $studentModel = new StudentModel();
                            $students = $studentModel->getAllStudents();
                            foreach($students as $student): 
                            ?>
                            <tr>
                                <td><?php echo $student['student_id']; ?></td>
                                <td><?php echo $student['full_name']; ?></td>
                                <td><?php echo $student['email']; ?></td>
                                <td><?php echo $student['course']; ?></td>
                                <td><?php echo $student['year_level']; ?></td>
                                <td><?php echo $student['status']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport() {
    var printContents = document.getElementById('reportContent').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>