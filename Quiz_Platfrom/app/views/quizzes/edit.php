<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz - LMS</title>
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
            <a href="index.php?controller=quiz&action=index">Quizzes</a>
            <a href="index.php?controller=auth&action=profile">Profile</a>
            <a href="index.php?controller=auth&action=logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="form-card">
            <h2>✏️ Edit Quiz</h2>
            
            <?php if(isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=quiz&action=edit&id=<?php echo $quiz['id']; ?>">
                <div class="form-group">
                    <label>📚 Select Course *</label>
                    <select name="course_id" required>
                        <option value="">-- Select a course --</option>
                        <?php if(isset($courses) && count($courses) > 0): ?>
                            <?php foreach($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo ($quiz['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?> (<?php echo htmlspecialchars($course['subject']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>📝 Quiz Title *</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($quiz['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label>📄 Description</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>⏱️ Time Limit (minutes)</label>
                        <input type="number" name="time_limit" value="<?php echo $quiz['time_limit']; ?>" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>⭐ Total Marks</label>
                        <input type="number" name="total_marks" value="<?php echo $quiz['total_marks']; ?>" min="1" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>🎯 Pass Mark</label>
                        <input type="number" name="pass_mark" value="<?php echo $quiz['pass_mark']; ?>" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>📋 Quiz Type</label>
                        <select name="type" required>
                            <option value="graded" <?php echo ($quiz['type'] == 'graded') ? 'selected' : ''; ?>>Graded (Counts for marks)</option>
                            <option value="practice" <?php echo ($quiz['type'] == 'practice') ? 'selected' : ''; ?>>Practice (For practice only)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>📅 Start Date</label>
                        <input type="date" name="start_date" value="<?php echo $quiz['start_date']; ?>">
                        <div class="info-text">Leave empty for no start date restriction</div>
                    </div>
                    
                    <div class="form-group">
                        <label>📅 End Date</label>
                        <input type="date" name="end_date" value="<?php echo $quiz['end_date']; ?>">
                        <div class="info-text">Leave empty for no end date restriction</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🔘 Quiz Status</label>
                    <select name="status" required>
                        <option value="draft" <?php echo ($quiz['status'] == 'draft') ? 'selected' : ''; ?>>Draft (Not visible to students)</option>
                        <option value="published" <?php echo ($quiz['status'] == 'published') ? 'selected' : ''; ?>>Published (Visible to students)</option>
                    </select>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-submit">💾 Save Changes</button>
                    <a href="index.php?controller=quiz&action=index" class="btn-back">← Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>