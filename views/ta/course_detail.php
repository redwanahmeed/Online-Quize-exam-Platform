<?php $page_title = 'Course Details'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <span><?php echo htmlspecialchars($course['title']); ?></span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title"><?php echo htmlspecialchars($course['title']); ?></div>
        <div class="page-subtitle"><?php echo htmlspecialchars($course['subject_name']); ?> &bull; Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></div>
    </div>
    <div class="page-header-actions">
        <a href="/ta_project/controllers/QuizController.php?action=create&course_id=<?php echo $course['id']; ?>" class="btn btn-accent">+ Practice Quiz</a>
        <a href="/ta_project/controllers/AnnouncementController.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary">Announcement</a>
    </div>
</div>

<!-- Summary stats -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
    <div class="stat-card">
        <div class="stat-icon blue">👤</div>
        <div><div class="stat-label">Students</div><div class="stat-value"><?php echo $summary['total_students']; ?></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold">✎</div>
        <div><div class="stat-label">Quizzes</div><div class="stat-value"><?php echo $summary['total_quizzes']; ?></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">▤</div>
        <div><div class="stat-label">Attempts</div><div class="stat-value"><?php echo $summary['total_attempts']; ?></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">%</div>
        <div><div class="stat-label">Avg Score</div><div class="stat-value"><?php echo $summary['avg_score']; ?>%</div></div>
    </div>
</div>

<!-- Quick Nav -->
<div style="display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
    <a href="/ta_project/controllers/ResultsController.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline">📊 Results</a>
    <a href="/ta_project/controllers/ResultsController.php?action=at_risk&course_id=<?php echo $course['id']; ?>" class="btn btn-outline">⚑ At-Risk Students</a>
    <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline">✉ Q&amp;A Board</a>
    <a href="/ta_project/controllers/MaterialController.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline">⇪ Materials</a>
    <a href="/ta_project/controllers/DoubtSessionController.php?action=create&course_id=<?php echo $course['id']; ?>" class="btn btn-outline">◷ Schedule Session</a>
</div>

<!-- Quizzes -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <div class="card-title">Practice Quizzes</div>
        <a href="/ta_project/controllers/QuizController.php?action=create&course_id=<?php echo $course['id']; ?>" class="btn btn-accent btn-sm">+ New Quiz</a>
    </div>
    <?php if (empty($quizzes)): ?>
    <div class="card-body">
        <div class="empty-state"><div class="empty-icon">✎</div><p>No quizzes created yet.</p></div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Questions</th><th>Attempts</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($quizzes as $q): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($q['title']); ?></strong></td>
                    <td><span class="badge badge-navy"><?php echo ucfirst($q['quiz_type']); ?></span></td>
                    <td>
                        <?php $sc = $q['status'] === 'published' ? 'badge-green' : 'badge-grey'; ?>
                        <span class="badge <?php echo $sc; ?>"><?php echo ucfirst($q['status']); ?></span>
                    </td>
                    <td><?php echo $q['question_count']; ?></td>
                    <td><?php echo $q['attempt_count']; ?></td>
                    <td style="display:flex;gap:6px;">
                        <a href="/ta_project/controllers/QuizController.php?action=questions&quiz_id=<?php echo $q['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-primary btn-xs">Questions</a>
                        <a href="/ta_project/controllers/QuizController.php?action=edit&quiz_id=<?php echo $q['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-outline btn-xs">Edit</a>
                        <a href="/ta_project/controllers/QuizController.php?action=delete&quiz_id=<?php echo $q['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this quiz?')">Del</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Students -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Enrolled Students (<?php echo count($students); ?>)</div>
        <a href="/ta_project/controllers/ResultsController.php?action=at_risk&course_id=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm">⚑ At-Risk Filter</a>
    </div>
    <?php if (empty($students)): ?>
    <div class="card-body">
        <div class="empty-state"><div class="empty-icon">👤</div><p>No students enrolled yet.</p></div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Student ID</th><th>Program</th><th>Enrolled</th></tr></thead>
            <tbody>
                <?php foreach ($students as $i => $s): ?>
                <tr>
                    <td class="text-muted"><?php echo $i+1; ?></td>
                    <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                    <td class="text-small"><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['student_id'] ?? '—'); ?></td>
                    <td class="text-small"><?php echo htmlspecialchars($s['program'] ?? '—'); ?></td>
                    <td class="text-small"><?php echo date('d M Y', strtotime($s['enrolled_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
