<!DOCTYPE html>
<html>
<head>
    <title>Create Quiz - LMS</title>
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
        select option[value=""]{color:#999;}
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
            <h2>➕ Create New Quiz</h2>
            
            <?php if(isset($error)): ?>
                <div class="error">❌ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>📚 Select Course *</label>
                    <select name="course_id" required>
                        <option value="">-- Select a course --</option>
                        <?php if(isset($courses) && count($courses) > 0): ?>
                            <?php foreach($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>">
                                    <?php echo htmlspecialchars($course['title']); ?> (<?php echo htmlspecialchars($course['subject']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No courses found. Please create a course first.</option>
                        <?php endif; ?>
                    </select>
                    <div class="info-text">Select the course this quiz belongs to</div>
                </div>
                
                <div class="form-group">
                    <label>📝 Quiz Title *</label>
                    <input type="text" name="title" required placeholder="e.g., Mid Term Examination 2024">
                </div>
                
                <div class="form-group">
                    <label>📄 Description</label>
                    <textarea name="description" rows="3" placeholder="Describe what this quiz covers..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>⏱️ Time Limit (minutes)</label>
                        <input type="number" name="time_limit" value="30" min="1" required>
                        <div class="info-text">Time students have to complete the quiz</div>
                    </div>
                    
                    <div class="form-group">
                        <label>⭐ Total Marks</label>
                        <input type="number" name="total_marks" value="100" min="1" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>🎯 Pass Mark</label>
                        <input type="number" name="pass_mark" value="50" min="1" required>
                        <div class="info-text">Minimum marks required to pass</div>
                    </div>
                    
                    <div class="form-group">
                        <label>📋 Quiz Type</label>
                        <select name="type" required>
                            <option value="graded">Graded (Counts for marks)</option>
                            <option value="practice">Practice (For practice only)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>📅 Start Date</label>
                        <input type="date" name="start_date">
                        <div class="info-text">Leave empty for no start date restriction</div>
                    </div>
                    
                    <div class="form-group">
                        <label>📅 End Date</label>
                        <input type="date" name="end_date">
                        <div class="info-text">Leave empty for no end date restriction</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🔘 Quiz Status</label>
                    <select name="status" required>
                        <option value="draft">Draft (Save as draft, not visible to students)</option>
                        <option value="published">Published (Visible to students)</option>
                    </select>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-submit">✅ Create Quiz</button>
                    <a href="index.php?controller=quiz&action=index" class="btn-back">← Cancel</a>
                </div>
            </form>
            
            <?php if(!isset($courses) || count($courses) == 0): ?>
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px;">
                    <strong>⚠️ No courses found!</strong>
                    <p>You need to create a course before creating a quiz.</p>
                    <a href="index.php?controller=course&action=create" class="btn" style="background:#28a745; color:white; padding:8px 15px; text-decoration:none; border-radius:5px; display:inline-block; margin-top:10px;">+ Create Course First</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>