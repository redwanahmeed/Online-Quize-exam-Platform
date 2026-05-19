<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - LMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:#f4f6f9;}
        .header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
        .nav a{color:white;text-decoration:none;margin:0 10px;padding:5px 10px;border-radius:5px;}
        .nav a:hover{background:rgba(255,255,255,0.2);}
        .container{max-width:1200px;margin:30px auto;padding:0 20px;}
        
        /* Welcome Card */
        .welcome-card{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:30px;border-radius:15px;margin-bottom:30px;}
        .welcome-card h2{font-size:28px;margin-bottom:10px;}
        .welcome-card p{opacity:0.9;}
        
        /* Stats Grid */
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:30px;}
        .stat-card{background:white;padding:20px;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,0.1);text-align:center;transition:transform 0.3s;}
        .stat-card:hover{transform:translateY(-5px);}
        .stat-card h3{color:#666;font-size:13px;margin-bottom:8px;}
        .stat-number{font-size:32px;font-weight:bold;color:#667eea;}
        .stat-label{color:#999;font-size:11px;margin-top:5px;}
        
        /* Sections */
        .section{margin-bottom:40px;}
        .section-title{font-size:20px;margin-bottom:15px;padding-bottom:10px;border-bottom:2px solid #ddd;}
        
        /* Course Grid */
        .course-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;}
        .course-card{background:white;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.1);transition:transform 0.3s;}
        .course-card:hover{transform:translateY(-3px);}
        .course-title{font-size:18px;margin-bottom:5px;color:#333;}
        .course-instructor{color:#667eea;font-size:12px;margin-bottom:10px;}
        .course-subject{color:#666;margin-bottom:10px;font-size:14px;}
        .course-desc{color:#888;margin-bottom:15px;font-size:13px;line-height:1.4;}
        .course-stats{display:flex;gap:15px;margin-bottom:15px;font-size:12px;}
        .course-stats span{background:#f0f0f0;padding:3px 8px;border-radius:5px;}
        .status{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:bold;display:inline-block;margin-bottom:10px;}
        .status.published{background:#d4edda;color:#155724;}
        .status.draft{background:#fff3cd;color:#856404;}
        .btn{display:inline-block;padding:8px 15px;border-radius:5px;text-decoration:none;font-size:12px;}
        .btn-primary{background:#667eea;color:white;}
        .btn-primary:hover{background:#5a67d8;}
        .btn-success{background:#28a745;color:white;}
        .btn-sm{padding:5px 10px;font-size:11px;}
        
        /* Table */
        .data-table{width:100%;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .data-table th,.data-table td{padding:12px;text-align:left;border-bottom:1px solid #ddd;}
        .data-table th{background:#667eea;color:white;}
        .empty{text-align:center;padding:40px;background:white;border-radius:10px;color:#999;}
        footer{text-align:center;padding:20px;color:#999;margin-top:40px;}
        .badge{padding:3px 8px;border-radius:20px;font-size:11px;}
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
        <a href="index.php?controller=quiz&action=index">Quizzes</a>
        <a href="index.php?controller=ta&action=index">TA Management</a>
        <a href="index.php?controller=auth&action=profile">Profile</a>
        <a href="index.php?controller=auth&action=logout">Logout</a>
    </div>
</div>
    
    <div class="container">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h2>
            <p>Role: <?php echo ucfirst($user['role']); ?> | Department: <?php echo htmlspecialchars($user['department'] ?: 'Not specified'); ?></p>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📚 Total Courses</h3>
                <div class="stat-number"><?php echo $stats['total_courses']; ?></div>
                <div class="stat-label">All courses in system</div>
            </div>
            <?php if($user['role'] == 'instructor'): ?>
            <div class="stat-card">
                <h3>✅ My Courses</h3>
                <div class="stat-number"><?php echo $stats['my_courses']; ?></div>
                <div class="stat-label">Courses you created</div>
            </div>
            <div class="stat-card">
                <h3>👨‍🎓 My Students</h3>
                <div class="stat-number"><?php echo $stats['total_students']; ?></div>
                <div class="stat-label">Total enrolled students</div>
            </div>
            <div class="stat-card">
                <h3>📝 My Quizzes</h3>
                <div class="stat-number"><?php echo $stats['total_quizzes']; ?></div>
                <div class="stat-label">Quizzes you created</div>
            </div>
            <?php else: ?>
            <div class="stat-card">
                <h3>✅ Enrolled</h3>
                <div class="stat-number"><?php echo $stats['enrolled_courses']; ?></div>
                <div class="stat-label">Courses you joined</div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- All Courses Section (সব ইউজার দেখতে পাবে) -->
        <div class="section">
            <h2 class="section-title">📖 All Available Courses</h2>
            
            <?php if(count($allCourses) > 0): ?>
            <div class="course-grid">
                <?php foreach($allCourses as $course): ?>
                <div class="course-card">
                    <span class="status <?php echo $course['status']; ?>">
                        <?php echo ucfirst($course['status']); ?>
                    </span>
                    <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="course-instructor">👨‍🏫 Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                    <p class="course-subject">📚 Subject: <?php echo htmlspecialchars($course['subject']); ?></p>
                    <p class="course-desc"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                    <div class="course-stats">
                        <span>👥 Enrolled: <?php echo isset($course['enrolled_students']) ? $course['enrolled_students'] : 0; ?> students</span>
                        <span>📋 <?php echo ucfirst($course['enrollment_type']); ?></span>
                        <span>👥 Max: <?php echo $course['max_students']; ?></span>
                    </div>
                    <?php if($user['role'] != 'instructor'): ?>
                    <a href="#" class="btn btn-success btn-sm" onclick="alert('Enrollment feature coming soon! Contact your instructor.')">📝 Enroll Now</a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty">
                <p>😊 No courses available yet.</p>
                <?php if($user['role'] == 'instructor'): ?>
                <a href="index.php?controller=course&action=create" class="btn btn-primary" style="margin-top:15px;">+ Create Your First Course</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- My Courses Section (শুধু instructor এর জন্য) -->
        <?php if($user['role'] == 'instructor' && count($myCourses) > 0): ?>
        <div class="section">
            <h2 class="section-title">✏️ My Created Courses</h2>
            <div class="course-grid">
                <?php foreach($myCourses as $course): ?>
                <div class="course-card">
                    <span class="status <?php echo $course['status']; ?>">
                        <?php echo ucfirst($course['status']); ?>
                    </span>
                    <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="course-subject">📚 <?php echo htmlspecialchars($course['subject']); ?></p>
                    <div class="course-stats">
                        <span>👥 Enrolled: <?php echo isset($course['enrolled_students']) ? $course['enrolled_students'] : 0; ?> students</span>
                    </div>
                    <a href="index.php?controller=course&action=edit&id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">Manage Course →</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Enrolled Courses Section (শুধু student এর জন্য) -->
        <?php if($user['role'] != 'instructor' && count($enrolledCourses) > 0): ?>
        <div class="section">
            <h2 class="section-title">✅ My Enrolled Courses</h2>
            <div class="course-grid">
                <?php foreach($enrolledCourses as $course): ?>
                <div class="course-card">
                    <span class="status published">Enrolled</span>
                    <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="course-subject">📚 <?php echo htmlspecialchars($course['subject']); ?></p>
                    <a href="#" class="btn btn-primary btn-sm">View Course →</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Pending Requests (শুধু instructor এর জন্য) -->
        <?php if($user['role'] == 'instructor' && count($pendingRequests) > 0): ?>
        <div class="section">
            <h2 class="section-title">⏳ Pending Enrollment Requests</h2>
            <table class="data-table">
                <thead>
                    <tr><th>Student</th><th>Course</th><th>Request Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach($pendingRequests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['course_title']); ?></td>
                        <td><?php echo date('d M Y', strtotime($request['enrolled_at'])); ?></td>
                        <td>
                            <a href="index.php?controller=enrollment&action=approve&id=<?php echo $request['id']; ?>&course_id=<?php echo $request['course_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                            <a href="index.php?controller=enrollment&action=reject&id=<?php echo $request['id']; ?>&course_id=<?php echo $request['course_id']; ?>" class="btn btn-primary btn-sm">Reject</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; 2024 LMS System. All rights reserved.</p>
    </footer>
</body>
</html>