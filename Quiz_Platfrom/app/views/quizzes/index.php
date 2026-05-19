<!DOCTYPE html>
<html>
<head>
    <title>Quiz Management - LMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:#f4f6f9;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
        .nav a{color:white;text-decoration:none;margin:0 10px;padding:5px 10px;border-radius:5px;}
        .nav a:hover{background:rgba(255,255,255,0.2);}
        .container{max-width:1200px;margin:30px auto;padding:0 20px;}
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}
        .btn{display:inline-block;padding:10px 20px;border-radius:5px;text-decoration:none;}
        .btn-primary{background:#667eea;color:white;}
        .btn-primary:hover{background:#5a67d8;}
        .btn-sm{padding:5px 10px;font-size:12px;}
        .btn-info{background:#17a2b8;color:white;}
        .btn-warning{background:#ffc107;color:#333;}
        .btn-danger{background:#dc3545;color:white;}
        table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        th,td{padding:15px;text-align:left;border-bottom:1px solid #ddd;}
        th{background:#667eea;color:white;}
        tr:hover{background:#f5f5f5;}
        .status{padding:3px 10px;border-radius:20px;font-size:11px;display:inline-block;}
        .status.published{background:#d4edda;color:#155724;}
        .status.draft{background:#fff3cd;color:#856404;}
        .empty{text-align:center;padding:40px;background:white;border-radius:10px;color:#999;}
        .alert{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin-bottom:20px;}
    </style>
</head>
<body>
    <div class="header">
        <h2>📚 LMS - Instructor Panel</h2>
        <div class="nav">
            <a href="index.php?controller=dashboard&action=index">Dashboard</a>
            <a href="index.php?controller=course&action=index">Courses</a>
            <a href="index.php?controller=quiz&action=index">Quizzes</a>
            <a href="index.php?controller=auth&action=profile">Profile</a>
            <a href="index.php?controller=auth&action=logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert">
                <?php 
                    if($_GET['success'] == 'created') echo "✅ Quiz created successfully!";
                    if($_GET['success'] == 'updated') echo "✅ Quiz updated successfully!";
                    if($_GET['success'] == 'deleted') echo "✅ Quiz deleted successfully!";
                ?>
            </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h1>📝 Quiz Management</h1>
            <a href="index.php?controller=quiz&action=create" class="btn btn-primary">+ Create New Quiz</a>
        </div>
        
        <?php if(isset($quizzes) && count($quizzes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Quiz Title</th>
                        <th>Course</th>
                        <th>Time Limit</th>
                        <th>Total Marks</th>
                        <th>Pass Mark</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($quizzes as $quiz): ?>
                    <tr>
                        <td><?php echo $quiz['id']; ?></td>
                        <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                        <td><?php echo htmlspecialchars($quiz['course_title'] ?? 'N/A'); ?></td>
                        <td><?php echo $quiz['time_limit']; ?> min</td>
                        <td><?php echo $quiz['total_marks']; ?></td>
                        <td><?php echo $quiz['pass_mark']; ?></td>
                        <td><?php echo ucfirst($quiz['type']); ?></td>
                        <td>
                            <span class="status <?php echo $quiz['status']; ?>">
                                <?php echo ucfirst($quiz['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?controller=quiz&action=edit&id=<?php echo $quiz['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <a href="index.php?controller=quiz&action=stats&id=<?php echo $quiz['id']; ?>" class="btn btn-info btn-sm">📊 Stats</a>
                            <a href="index.php?controller=quiz&action=delete&id=<?php echo $quiz['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this quiz?')">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">
                <p>😊 No quizzes created yet.</p>
                <a href="index.php?controller=quiz&action=create" class="btn btn-primary">Create Your First Quiz</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>