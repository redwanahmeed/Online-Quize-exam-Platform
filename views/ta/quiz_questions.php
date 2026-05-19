<?php $page_title = 'Manage Questions'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <span><?php echo htmlspecialchars($quiz['title']); ?></span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title"><?php echo htmlspecialchars($quiz['title']); ?></div>
        <div class="page-subtitle">
            <?php echo count($questions); ?> question(s) &bull;
            Total Marks: <?php echo $quiz['total_marks']; ?> &bull;
            Time: <?php echo $quiz['time_limit_minutes']; ?> min &bull;
            <span class="badge <?php echo $quiz['status'] === 'published' ? 'badge-green' : 'badge-gold'; ?>">
                <?php echo $quiz['status'] === 'published' ? 'Published' : 'Draft (Pending Approval)'; ?>
            </span>
        </div>
    </div>
    <div class="page-header-actions">
        <a href="/ta_project/controllers/QuizController.php?action=add_question&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-accent">+ Add Question</a>
        <a href="/ta_project/controllers/QuizController.php?action=edit&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-outline btn-sm">Edit Quiz Info</a>
    </div>
</div>

<?php if (empty($questions)): ?>
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">✎</div>
            <p>No questions yet. Add your first question to get started.</p>
            <a href="/ta_project/controllers/QuizController.php?action=add_question&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-accent" style="margin-top:14px;display:inline-flex;">+ Add First Question</a>
        </div>
    </div>
</div>
<?php else: ?>

<?php foreach ($questions as $idx => $q): ?>
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="background:#f8fafd;">
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="background:var(--navy);color:#fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">
                <?php echo $idx + 1; ?>
            </span>
            <div class="card-title" style="font-size:14px;"><?php echo htmlspecialchars($q['question_text']); ?></div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span class="badge badge-gold"><?php echo $q['marks']; ?> mark<?php echo $q['marks'] != 1 ? 's' : ''; ?></span>
            <a href="/ta_project/controllers/QuizController.php?action=edit_question&question_id=<?php echo $q['id']; ?>&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-outline btn-xs">Edit</a>
            <a href="/ta_project/controllers/QuizController.php?action=delete_question&question_id=<?php echo $q['id']; ?>&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this question?')">Delete</a>
        </div>
    </div>
    <div class="card-body" style="padding:16px 24px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <?php foreach ($q['options'] as $opt): ?>
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:7px;border:1.5px solid <?php echo $opt['is_correct'] ? '#2e7d5e' : 'var(--border)'; ?>;background:<?php echo $opt['is_correct'] ? '#d5f0e5' : '#fff'; ?>;">
                <span style="font-size:14px;"><?php echo $opt['is_correct'] ? '✓' : '○'; ?></span>
                <span style="font-size:13px;color:<?php echo $opt['is_correct'] ? '#1b6a42' : 'var(--text)'; ?>;font-weight:<?php echo $opt['is_correct'] ? '600' : '400'; ?>">
                    <?php echo htmlspecialchars($opt['option_text']); ?>
                </span>
                <?php if ($opt['is_correct']): ?>
                <span class="badge badge-green" style="margin-left:auto;font-size:10px;">Correct</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($q['options'])): ?>
        <p class="text-muted text-small">No options added yet.</p>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<div style="margin-top:16px;display:flex;gap:10px;">
    <a href="/ta_project/controllers/QuizController.php?action=add_question&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-accent">+ Add Another Question</a>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>" class="btn btn-outline">Back to Course</a>
</div>
<?php endif; ?>
