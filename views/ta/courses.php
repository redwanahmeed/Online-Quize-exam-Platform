<?php $page_title = 'My Courses'; ?>
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">My Courses</div>
        <div class="page-subtitle">Courses you are assigned to as Teaching Assistant</div>
    </div>
</div>

<?php if (empty($courses)): ?>
<div class="empty-state">
    <div class="empty-icon">◫</div>
    <p>You are not assigned to any courses yet. Contact your instructor.</p>
</div>
<?php else: ?>
<div class="courses-grid">
    <?php foreach ($courses as $c): ?>
    <div class="course-card">
        <div class="course-card-header">
            <div class="course-card-title"><?php echo htmlspecialchars($c['title']); ?></div>
            <div class="course-card-subject"><?php echo htmlspecialchars($c['subject_name']); ?></div>
        </div>
        <div class="course-card-body">
            <div class="course-meta">
                <div class="course-meta-item">
                    <strong><?php echo $c['student_count']; ?></strong>
                    Students
                </div>
                <div class="course-meta-item">
                    <strong><?php echo htmlspecialchars($c['instructor_name']); ?></strong>
                    Instructor
                </div>
                <div class="course-meta-item">
                    <?php
                    $statusMap = ['active' => 'badge-green', 'draft' => 'badge-grey', 'archived' => 'badge-navy'];
                    $cls = $statusMap[$c['status']] ?? 'badge-grey';
                    ?>
                    <span class="badge <?php echo $cls; ?>"><?php echo ucfirst($c['status']); ?></span>
                </div>
            </div>
            <?php if ($c['description']): ?>
            <p style="font-size:13px;color:#6b7c99;margin-bottom:12px;"><?php echo htmlspecialchars(substr($c['description'], 0, 80)); ?>...</p>
            <?php endif; ?>
            <div class="course-card-actions">
                <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $c['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                <a href="/ta_project/controllers/QuizController.php?action=create&course_id=<?php echo $c['id']; ?>" class="btn btn-accent btn-sm">+ Quiz</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
