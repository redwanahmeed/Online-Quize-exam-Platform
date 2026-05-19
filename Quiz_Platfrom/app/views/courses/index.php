<!DOCTYPE html>
<html>
<head>
    <title>Course Management - LMS</title>
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
        .btn-success{background:#28a745;color:white;}
        .btn-warning{background:#ffc107;color:#333;}
        .btn-info{background:#17a2b8;color:white;}
        .btn-danger{background:#dc3545;color:white;}
        .btn-secondary{background:#6c757d;color:white;}
        .section{margin-bottom:40px;}
        .section-title{font-size:20px;margin-bottom:15px;padding-bottom:10px;border-bottom:2px solid #ddd;}
        .course-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px;}
        .course-card{background:white;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .course-title{font-size:18px;margin-bottom:10px;color:#333;}
        .course-subject{color:#666;margin-bottom:10px;font-size:14px;}
        .course-desc{color:#888;margin-bottom:15px;font-size:13px;line-height:1.4;}
        .course-stats{display:flex;gap:15px;margin-bottom:15px;font-size:12px;}
        .course-stats span{background:#f0f0f0;padding:3px 8px;border-radius:5px;}
        .status{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:bold;display:inline-block;margin-bottom:10px;}
        .status.published{background:#d4edda;color:#155724;}
        .status.draft{background:#fff3cd;color:#856404;}
        .status.archived{background:#f8d7da;color:#721c24;}
        .course-actions{margin-top:15px;display:flex;gap:10px;flex-wrap:wrap;}
        .empty{text-align:center;padding:40px;background:white;border-radius:10px;color:#999;}
        footer{text-align:center;padding:20px;color:#999;margin-top:40px;}
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
                    if($_GET['success'] == 'created') echo "✅ Course created successfully!";
                    if($_GET['success'] == 'updated') echo "✅ Course updated successfully!";
                    if($_GET['success'] == 'archived') echo "✅ Course archived successfully!";
                    if($_GET['success'] == 'published') echo "✅ Course published successfully!";
                    if($_GET['success'] == 'unpublished') echo "✅ Course moved to draft!";
                ?>
            </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h1>📖 Course Management</h1>
            <a href="index.php?controller=course&action=create" class="btn btn-primary">+ Create New Course</a>
        </div>
        
        <!-- Published Courses Section -->
        <div class="section">
            <h2 class="section-title">📌 Published Courses</h2>
            <div class="course-grid">
                <?php if(isset($publishedCourses) && count($publishedCourses) > 0): ?>
                    <?php foreach($publishedCourses as $course): ?>
                    <div class="course-card">
                        <span class="status published">Published</span>
                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="course-subject">📚 <?php echo htmlspecialchars($course['subject']); ?></p>
                        <p class="course-desc"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                        <div class="course-stats">
                            <span>👨‍🎓 Students: <?php echo isset($course['student_count']) ? $course['student_count'] : 0; ?></span>
                            <span>📋 <?php echo ucfirst($course['enrollment_type']); ?></span>
                            <span>👥 Max: <?php echo $course['max_students']; ?></span>
                        </div>
                        <div class="course-actions">
                            <a href="index.php?controller=course&action=edit&id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <a href="index.php?controller=course&action=students&id=<?php echo $course['id']; ?>" class="btn btn-info btn-sm">👨‍🎓 Students</a>
                            <a href="index.php?controller=enrollment&action=requests&course_id=<?php echo $course['id']; ?>" class="btn btn-secondary btn-sm">📝 Requests</a>
                            <a href="index.php?controller=course&action=unpublish&id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Move to draft?')">📄 Draft</a>
                            <a href="index.php?controller=course&action=archive&id=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Archive this course?')">🗄️ Archive</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">No published courses yet. Publish a course to see it here.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Draft Courses Section -->
        <div class="section">
            <h2 class="section-title">✏️ Draft Courses</h2>
            <div class="course-grid">
                <?php if(isset($draftCourses) && count($draftCourses) > 0): ?>
                    <?php foreach($draftCourses as $course): ?>
                    <div class="course-card">
                        <span class="status draft">Draft</span>
                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="course-subject">📚 <?php echo htmlspecialchars($course['subject']); ?></p>
                        <p class="course-desc"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                        <div class="course-stats">
                            <span>📋 <?php echo ucfirst($course['enrollment_type']); ?></span>
                            <span>👥 Max: <?php echo $course['max_students']; ?></span>
                        </div>
                        <div class="course-actions">
                            <a href="index.php?controller=course&action=edit&id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <a href="index.php?controller=course&action=publish&id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm">📢 Publish</a>
                            <a href="index.php?controller=course&action=archive&id=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Archive this course?')">🗄️ Archive</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">No draft courses. Create a new course as draft.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Archived Courses Section -->
        <div class="section">
            <h2 class="section-title">🗄️ Archived Courses</h2>
            <div class="course-grid">
                <?php if(isset($archivedCourses) && count($archivedCourses) > 0): ?>
                    <?php foreach($archivedCourses as $course): ?>
                    <div class="course-card">
                        <span class="status archived">Archived</span>
                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="course-subject">📚 <?php echo htmlspecialchars($course['subject']); ?></p>
                        <div class="course-actions">
                            <a href="index.php?controller=course&action=edit&id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <a href="index.php?controller=course&action=publish&id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm">📢 Restore & Publish</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">No archived courses.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2024 LMS System. All rights reserved.</p>
    </footer>
</body>
</html>