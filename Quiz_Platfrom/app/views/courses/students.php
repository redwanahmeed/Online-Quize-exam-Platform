<!DOCTYPE html>
<html>
<head>
    <title>Enrolled Students - <?php echo htmlspecialchars($course['title']); ?></title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:#f4f6f9;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
        .nav a{color:white;text-decoration:none;margin:0 10px;}
        .container{max-width:1000px;margin:30px auto;padding:0 20px;}
        .card{background:white;border-radius:10px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        h2{margin-bottom:10px;}
        .course-info{background:#f0f0f0;padding:15px;border-radius:8px;margin:20px 0;display:flex;gap:20px;}
        .stats{display:flex;gap:20px;margin-bottom:20px;}
        .stat-box{background:#667eea;color:white;padding:15px;border-radius:8px;flex:1;text-align:center;}
        table{width:100%;border-collapse:collapse;margin-top:20px;}
        th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd;}
        th{background:#667eea;color:white;}
        .empty{text-align:center;padding:40px;color:#999;}
        .btn-back{display:inline-block;background:#6c757d;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-top:20px;}
        .badge{display:inline-block;padding:3px 8px;border-radius:20px;font-size:11px;}
        .badge-approved{background:#d4edda;color:#155724;}
        .badge-pending{background:#fff3cd;color:#856404;}
    </style>
</head>
<body>
    <div class="header">
        <h2>📚 LMS - Instructor Panel</h2>
        <div class="nav">
            <a href="index.php?controller=dashboard&action=index">Dashboard</a>
            <a href="index.php?controller=course&action=index">Courses</a>
            <a href="index.php?controller=auth&action=profile">Profile</a>
            <a href="index.php?controller=auth&action=logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h2>👨‍🎓 Enrolled Students</h2>
            <p>Course: <strong><?php echo htmlspecialchars($course['title']); ?></strong></p>
            
            <div class="course-info">
                <div>📚 Subject: <?php echo htmlspecialchars($course['subject']); ?></div>
                <div>🔓 Enrollment: <?php echo ucfirst($course['enrollment_type']); ?></div>
                <div>👥 Max: <?php echo $course['max_students']; ?></div>
            </div>
            
            <div class="stats">
                <div class="stat-box">
                    <h3><?php echo $enrolledCount; ?></h3>
                    <p>Approved Students</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $pendingCount; ?></h3>
                    <p>Pending Requests</p>
                    <a href="index.php?controller=enrollment&action=requests&course_id=<?php echo $course['id']; ?>" style="color:white;">View Requests →</a>
                </div>
            </div>
            
            <h3>📋 Student List</h3>
            
            <?php if(count($students) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Enrolled Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                        <td><?php echo date('d M Y', strtotime($student['enrolled_at'])); ?></td>
                        <td><span class="badge badge-approved">✅ Approved</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="empty">
                    <p>😊 No students enrolled yet.</p>
                    <p>Share your course with students to get enrollments.</p>
                </div>
            <?php endif; ?>
            
            <a href="index.php?controller=course&action=index" class="btn-back">← Back to Courses</a>
        </div>
    </div>
</body>
</html>