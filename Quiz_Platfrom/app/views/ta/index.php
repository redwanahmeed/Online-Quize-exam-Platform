<!DOCTYPE html>
<html>
<head>
    <title>TA Management - LMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:#f4f6f9;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
        .nav a{color:white;text-decoration:none;margin:0 10px;padding:5px 10px;border-radius:5px;}
        .nav a:hover{background:rgba(255,255,255,0.2);}
        .container{max-width:1200px;margin:30px auto;padding:0 20px;}
        .page-header{margin-bottom:30px;}
        h1{color:#333;}
        table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        th,td{padding:15px;text-align:left;border-bottom:1px solid #ddd;}
        th{background:#667eea;color:white;}
        tr:hover{background:#f5f5f5;}
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;background:#17a2b8;color:white;}
        .empty{text-align:center;padding:40px;background:white;border-radius:10px;color:#999;}
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
        <div class="page-header">
            <h1>Teaching Assistant Management</h1>
        </div>
        
        <h2>📋 All Teaching Assistants (<?php echo count($allTAs); ?>)</h2>
        
        <?php if(count($allTAs) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allTAs as $ta): ?>
                <tr>
                    <td><?php echo $ta['id']; ?></td>
                    <td><?php echo htmlspecialchars($ta['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($ta['email']); ?></td>
                    <td><?php echo htmlspecialchars($ta['username']); ?></td>
                    <td><?php echo htmlspecialchars($ta['department'] ?: 'N/A'); ?></td>
                    <td><span class="badge">Teaching Assistant</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty">
            <p> No Teaching Assistants found in the system.</p>
            
          
        </div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; 2024 LMS System. All rights reserved.</p>
    </footer>
</body>
</html>