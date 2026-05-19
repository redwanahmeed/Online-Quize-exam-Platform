<!DOCTYPE html>
<html>
<head>
    <title>Edit Course - LMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:#f4f6f9;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
        .nav a{color:white;text-decoration:none;margin:0 10px;padding:5px 10px;border-radius:5px;}
        .nav a:hover{background:rgba(255,255,255,0.2);}
        .container{max-width:800px;margin:40px auto;padding:0 20px;}
        .form-card{background:white;border-radius:10px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        h2{margin-bottom:25px;color:#333;border-bottom:2px solid #667eea;padding-bottom:10px;}
        .form-group{margin-bottom:20px;}
        label{display:block;margin-bottom:8px;font-weight:bold;color:#333;}
        input,select,textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px;font-size:14px;}
        textarea{resize:vertical;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
        .btn-submit{background:#28a745;color:white;padding:12px 25px;border:none;border-radius:5px;cursor:pointer;font-size:16px;}
        .btn-submit:hover{background:#218838;}
        .btn-back{background:#6c757d;color:white;padding:12px 25px;text-decoration:none;border-radius:5px;display:inline-block;margin-left:10px;}
        .btn-info{background:#17a2b8;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin-top:10px;}
        .btn-info:hover{background:#138496;}
        .error{background:#f8d7da;color:#721c24;padding:12px;border-radius:5px;margin-bottom:20px;}
        .status-badge{display:inline-block;padding:5px 12px;border-radius:20px;font-size:12px;margin-bottom:20px;}
        .status-draft{background:#fff3cd;color:#856404;}
        .status-published{background:#d4edda;color:#155724;}
        .status-archived{background:#f8d7da;color:#721c24;}
        .ta-section{margin-top:30px;padding-top:20px;border-top:2px solid #ddd;}
        .ta-section h3{color:#333;margin-bottom:10px;}
        .ta-section p{color:#666;margin-bottom:15px;font-size:14px;}
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
        <div class="form-card">
            <h2>✏️ Edit Course</h2>
            
            <div class="status-badge status-<?php echo $course['status']; ?>">
                Current Status: <?php echo ucfirst($course['status']); ?>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>📖 Course Title *</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($course['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label>📚 Subject *</label>
                    <input type="text" name="subject" required value="<?php echo htmlspecialchars($course['subject']); ?>">
                </div>
                
                <div class="form-group">
                    <label>📝 Description *</label>
                    <textarea name="description" rows="5" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>🔓 Enrollment Type</label>
                        <select name="enrollment_type">
                            <option value="open" <?php echo $course['enrollment_type'] == 'open' ? 'selected' : ''; ?>>Open (Auto Approve)</option>
                            <option value="approval" <?php echo $course['enrollment_type'] == 'approval' ? 'selected' : ''; ?>>Approval Required</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>👥 Maximum Students</label>
                        <input type="number" name="max_students" value="<?php echo $course['max_students']; ?>" min="1" max="999">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>📄 Course Status</label>
                    <select name="status">
                        <option value="draft" <?php echo $course['status'] == 'draft' ? 'selected' : ''; ?>>Draft (Not visible to students)</option>
                        <option value="published" <?php echo $course['status'] == 'published' ? 'selected' : ''; ?>>Published (Visible to students)</option>
                        <option value="archived" <?php echo $course['status'] == 'archived' ? 'selected' : ''; ?>>Archived (Hidden)</option>
                    </select>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-submit">💾 Save Changes</button>
                    <a href="index.php?controller=course&action=index" class="btn-back">← Cancel</a>
                </div>
            </form>
            
          
            <div class="ta-section">
                <h3>👨‍🏫 Teaching Assistant Management</h3>
                <p>Assign Teaching Assistants to help you manage this course. TAs can help answer student questions and assist with course management.</p>
                <a href="index.php?controller=ta&action=assign&course_id=<?php echo $course['id']; ?>" class="btn-info">📋 Manage TAs →</a>
            </div>
        </div>
    </div>
</body>
</html>