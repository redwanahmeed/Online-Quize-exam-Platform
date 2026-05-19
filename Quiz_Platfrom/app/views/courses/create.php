<!DOCTYPE html>
<html>
<head>
    <title>Create Course - LMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:#f4f6f9;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
        .nav a{color:white;text-decoration:none;margin:0 10px;}
        .container{max-width:700px;margin:40px auto;padding:0 20px;}
        .form-card{background:white;border-radius:10px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        h2{margin-bottom:25px;color:#333;}
        .form-group{margin-bottom:20px;}
        label{display:block;margin-bottom:8px;font-weight:bold;color:#333;}
        input,select,textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px;font-size:14px;}
        textarea{resize:vertical;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
        .btn-submit{background:#667eea;color:white;padding:12px 25px;border:none;border-radius:5px;cursor:pointer;font-size:16px;}
        .btn-submit:hover{background:#5a67d8;}
        .btn-back{background:#6c757d;color:white;padding:12px 25px;text-decoration:none;border-radius:5px;display:inline-block;margin-left:10px;}
        .error{background:#f8d7da;color:#721c24;padding:12px;border-radius:5px;margin-bottom:20px;}
        .info-text{font-size:12px;color:#999;margin-top:5px;}
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
        <div class="form-card">
            <h2>➕ Create New Course</h2>
            
            <?php if(isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>📖 Course Title *</label>
                    <input type="text" name="title" required placeholder="e.g., Introduction to Web Development">
                </div>
                
                <div class="form-group">
                    <label>📚 Subject *</label>
                    <input type="text" name="subject" required placeholder="e.g., Web Development, Programming, Database">
                </div>
                
                <div class="form-group">
                    <label>📝 Description *</label>
                    <textarea name="description" rows="5" required placeholder="Describe what students will learn in this course..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>🔓 Enrollment Type</label>
                        <select name="enrollment_type">
                            <option value="open">Open (Auto Approve)</option>
                            <option value="approval">Approval Required</option>
                        </select>
                        <div class="info-text">Open: Students can join immediately. Approval: You need to approve each student.</div>
                    </div>
                    
                    <div class="form-group">
                        <label>👥 Maximum Students</label>
                        <input type="number" name="max_students" value="50" min="1" max="999">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>📄 Course Status</label>
                    <select name="status">
                        <option value="draft">Save as Draft (Not visible to students)</option>
                        <option value="published">Publish (Visible to students)</option>
                    </select>
                    <div class="info-text">Draft: You can edit later. Published: Students can see and enroll.</div>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-submit">✅ Create Course</button>
                    <a href="index.php?controller=course&action=index" class="btn-back">← Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>