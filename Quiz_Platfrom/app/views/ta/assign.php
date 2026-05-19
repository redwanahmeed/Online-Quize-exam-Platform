<!DOCTYPE html>
<html>
<head>
    <title>Assign TA - <?php echo htmlspecialchars($course['title']); ?></title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:#f4f6f9;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
        .nav a{color:white;text-decoration:none;margin:0 10px;padding:5px 10px;border-radius:5px;}
        .nav a:hover{background:rgba(255,255,255,0.2);}
        .container{max-width:1000px;margin:30px auto;padding:0 20px;}
        .card{background:white;border-radius:10px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:25px;}
        h2{margin-bottom:20px;color:#333;border-bottom:2px solid #667eea;padding-bottom:10px;}
        h3{color:#333;margin-bottom:15px;}
        .form-group{margin-bottom:20px;}
        label{display:block;margin-bottom:8px;font-weight:bold;color:#333;}
        select{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px;font-size:14px;}
        .btn{display:inline-block;padding:10px 20px;border-radius:5px;text-decoration:none;border:none;cursor:pointer;}
        .btn-primary{background:#28a745;color:white;}
        .btn-primary:hover{background:#218838;}
        .btn-danger{background:#dc3545;color:white;}
        .btn-danger:hover{background:#c82333;}
        .btn-back{background:#6c757d;color:white;display:inline-block;padding:10px 20px;border-radius:5px;text-decoration:none;}
        .btn-back:hover{background:#5a6268;}
        table{width:100%;border-collapse:collapse;margin-top:15px;}
        th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd;}
        th{background:#f8f9fa;color:#333;}
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;background:#17a2b8;color:white;}
        .alert{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin-bottom:20px;}
        .error{background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;margin-bottom:20px;}
        .course-info{background:#e8f4f8;padding:15px;border-radius:8px;margin-bottom:20px;}
        .empty{text-align:center;padding:30px;color:#999;}
        footer{text-align:center;padding:20px;color:#999;margin-top:40px;}
    </style>
</head>
<body>
    <div class="header">
        <h2>📚 LMS - Instructor Panel</h2>
        <div class="nav">
            <a href="index.php?controller=dashboard&action=index">Dashboard</a>
            <a href="index.php?controller=course&action=index">Courses</a>
            <a href="index.php?controller=quiz&action=index">Quizzes</a>
            <a href="index.php?controller=ta&action=index">TA Management</a>
            <a href="index.php?controller=auth&action=profile">Profile</a>
            <a href="index.php?controller=auth&action=logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert">
                <?php 
                    if($_GET['success'] == 'assigned') echo "✅ TA assigned successfully!";
                    if($_GET['success'] == 'removed') echo "✅ TA removed successfully!";
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Course Information -->
        <div class="card">
            <h2>📖 Course Information</h2>
            <div class="course-info">
                <p><strong>Course:</strong> <?php echo htmlspecialchars($course['title']); ?></p>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($course['subject']); ?></p>
                <p><strong>Status:</strong> <span class="badge"><?php echo ucfirst($course['status']); ?></span></p>
            </div>
            <a href="index.php?controller=course&action=edit&id=<?php echo $course['id']; ?>" class="btn-back">← Back to Course</a>
        </div>
        
        <!-- Assign New TA -->
        <div class="card">
            <h2>➕ Assign New Teaching Assistant</h2>
            
            <?php if(count($availableTAs) > 0): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Select Teaching Assistant:</label>
                    <select name="ta_id" required>
                        <option value="">-- Select TA --</option>
                        <?php foreach($availableTAs as $ta): ?>
                        <option value="<?php echo $ta['id']; ?>">
                            <?php echo htmlspecialchars($ta['full_name']); ?> (<?php echo htmlspecialchars($ta['email']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">+ Assign TA to Course</button>
            </form>
            <?php else: ?>
            <div class="empty">
                <p>😊 No available TAs to assign.</p>
                <p>Either no TAs exist in the system or all TAs are already assigned to this course.</p>
                <p style="margin-top:10px;">👉 <a href="index.php?controller=ta&action=index">View all TAs</a></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Assigned TAs List -->
        <div class="card">
            <h2>👨‍🏫 Currently Assigned TAs (<?php echo count($assignedTAs); ?>)</h2>
            
            <?php if(count($assignedTAs) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Assigned Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assignedTAs as $ta): ?>
                    <tr>
                        <td><?php echo $ta['id']; ?></td>
                        <td><?php echo htmlspecialchars($ta['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($ta['email']); ?></td>
                        <td><?php echo htmlspecialchars($ta['department'] ?: 'N/A'); ?></td>
                        <td><?php echo date('d M Y', strtotime($ta['assigned_at'])); ?></td>
                        <td>
                            <a href="index.php?controller=ta&action=remove&course_id=<?php echo $course['id']; ?>&ta_id=<?php echo $ta['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Remove this TA from this course?')">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty">
                <p>📌 No TAs assigned to this course yet.</p>
                <p>Assign a TA from the form above to help manage this course.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2024 LMS System. All rights reserved.</p>
    </footer>
</body>
</html>