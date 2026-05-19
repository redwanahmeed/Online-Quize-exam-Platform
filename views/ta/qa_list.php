<?php $page_title = 'Q&A Board'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <span>Q&amp;A Board</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Q&amp;A Discussion Board</div>
        <div class="page-subtitle"><?php echo htmlspecialchars($course['title']); ?> &bull; <?php echo count($qa_questions); ?> question(s)</div>
    </div>
</div>

<!-- Filter bar -->
<div style="display:flex;gap:10px;margin-bottom:20px;">
    <span class="badge badge-navy" style="padding:6px 14px;font-size:13px;">All (<?php echo count($qa_questions); ?>)</span>
    <?php
    $unresolved = array_filter($qa_questions, function($q){ return !$q['is_resolved']; });
    $resolved   = array_filter($qa_questions, function($q){ return  $q['is_resolved']; });
    ?>
    <span class="badge badge-red"   style="padding:6px 14px;font-size:13px;">Unresolved (<?php echo count($unresolved); ?>)</span>
    <span class="badge badge-green" style="padding:6px 14px;font-size:13px;">Resolved (<?php echo count($resolved); ?>)</span>
</div>

<div class="card">
    <?php if (empty($qa_questions)): ?>
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">✉</div>
            <p>No questions posted yet on this course's Q&amp;A board.</p>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($qa_questions as $q): ?>
    <div class="qa-item">
        <div class="qa-avatar"><?php echo strtoupper(substr($q['student_name'], 0, 1)); ?></div>
        <div class="qa-content">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>&action=view&qa_id=<?php echo $q['id']; ?>"
                   style="text-decoration:none;">
                    <div class="qa-title"><?php echo htmlspecialchars($q['title']); ?></div>
                </a>
                <?php if ($q['is_resolved']): ?>
                <span class="badge badge-green" style="font-size:10px;">✓ Resolved</span>
                <?php else: ?>
                <span class="badge badge-red" style="font-size:10px;">Open</span>
                <?php endif; ?>
            </div>
            <div class="qa-meta">
                By <strong><?php echo htmlspecialchars($q['student_name']); ?></strong>
                &bull; <?php echo date('d M Y, H:i', strtotime($q['created_at'])); ?>
                &bull; <?php echo $q['answer_count']; ?> answer(s)
            </div>
            <div style="font-size:13px;color:var(--text);margin-top:4px;">
                <?php echo htmlspecialchars(substr($q['body'], 0, 120)); ?><?php echo strlen($q['body']) > 120 ? '...' : ''; ?>
            </div>
        </div>
        <div style="flex-shrink:0;">
            <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>&action=view&qa_id=<?php echo $q['id']; ?>"
               class="btn btn-primary btn-sm">Answer</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
