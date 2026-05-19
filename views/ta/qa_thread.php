<?php $page_title = 'Q&A Thread'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>">Q&amp;A Board</a>
    <span>/</span>
    <span>Thread</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title"><?php echo htmlspecialchars($question['title']); ?></div>
        <div class="page-subtitle">
            Asked by <strong><?php echo htmlspecialchars($question['student_name']); ?></strong>
            &bull; <?php echo date('d M Y, H:i', strtotime($question['created_at'])); ?>
            &bull;
            <?php if ($question['is_resolved']): ?>
            <span class="badge badge-green">✓ Resolved</span>
            <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>&action=view&qa_id=<?php echo $question['id']; ?>&resolve=0"
               class="btn btn-outline btn-xs" style="margin-left:6px;">Mark Unresolved</a>
            <?php else: ?>
            <span class="badge badge-red">Open</span>
            <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>&action=view&qa_id=<?php echo $question['id']; ?>&resolve=1"
               class="btn btn-success btn-xs" style="margin-left:6px;">Mark Resolved</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="page-header-actions">
        <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline btn-sm">← Back to Board</a>
    </div>
</div>

<!-- Question Body -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex;gap:14px;">
            <div class="qa-avatar" style="width:44px;height:44px;font-size:16px;">
                <?php echo strtoupper(substr($question['student_name'], 0, 1)); ?>
            </div>
            <div>
                <div style="font-weight:600;margin-bottom:6px;">
                    <?php echo htmlspecialchars($question['student_name']); ?>
                    <span class="badge badge-navy" style="font-size:10px;margin-left:4px;">Student</span>
                </div>
                <div style="font-size:15px;line-height:1.7;color:var(--text);">
                    <?php echo nl2br(htmlspecialchars($question['body'])); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Answers -->
<div style="margin-bottom:20px;">
    <div style="font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:var(--navy);margin-bottom:12px;">
        <?php echo count($answers); ?> Answer(s)
    </div>

    <?php if (empty($answers)): ?>
    <div class="empty-state" style="padding:24px;">
        <div class="empty-icon" style="font-size:28px;">💬</div>
        <p>No answers yet. Be the first to respond!</p>
    </div>
    <?php else: ?>
    <?php foreach ($answers as $ans): ?>
    <div class="answer-block <?php echo $ans['is_endorsed'] ? 'endorsed' : ''; ?>" id="answer-block-<?php echo $ans['id']; ?>">
        <div class="answer-author" style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
            <div class="qa-avatar" style="width:28px;height:28px;font-size:11px;background:<?php echo $ans['author_role'] === 'ta' ? 'var(--slate)' : 'var(--navy)'; ?>">
                <?php echo strtoupper(substr($ans['author_name'], 0, 1)); ?>
            </div>
            <span><?php echo htmlspecialchars($ans['author_name']); ?></span>
            <?php if ($ans['author_role'] === 'ta'): ?>
            <span class="badge badge-ta" style="font-size:10px;">TA</span>
            <?php elseif ($ans['author_role'] === 'instructor'): ?>
            <span class="badge badge-navy" style="font-size:10px;">Instructor</span>
            <?php endif; ?>
            <?php if ($ans['is_endorsed']): ?>
            <span class="badge badge-green" style="font-size:10px;">★ Endorsed</span>
            <?php endif; ?>
            <span class="text-muted text-small" style="margin-left:auto;"><?php echo date('d M Y, H:i', strtotime($ans['created_at'])); ?></span>
        </div>
        <div class="answer-body" style="margin-bottom:8px;">
            <?php echo nl2br(htmlspecialchars($ans['body'])); ?>
        </div>
        <!-- Endorse button — uses AJAX -->
        <button
            class="endorse-btn <?php echo $ans['is_endorsed'] ? 'active' : ''; ?>"
            data-answer-id="<?php echo $ans['id']; ?>"
            data-endorsed="<?php echo $ans['is_endorsed'] ? '1' : '0'; ?>"
            onclick="toggleEndorse(<?php echo $ans['id']; ?>, this.getAttribute('data-endorsed') === '1')">
            <?php echo $ans['is_endorsed'] ? '★ Endorsed' : '☆ Endorse'; ?>
        </button>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Post Answer Form -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Post Your Answer</div>
        <span class="badge badge-ta">Replying as TA</span>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): echo '<div>✕ ' . htmlspecialchars($e) . '</div>'; endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/ta_project/controllers/QAController.php?course_id=<?php echo $course['id']; ?>&action=view&qa_id=<?php echo $question['id']; ?>">
            <input type="hidden" name="post_answer" value="1">
            <div class="form-group">
                <label for="qa_body">Your Answer *</label>
                <textarea id="qa_body" name="body" rows="4"
                          placeholder="Write a clear, helpful answer..."><?php echo htmlspecialchars($_POST['body'] ?? ''); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" onclick="if(!document.getElementById('qa_body').value.trim()){alert('Answer cannot be empty.');return false;}">
                    Post Answer
                </button>
            </div>
        </form>
    </div>
</div>
