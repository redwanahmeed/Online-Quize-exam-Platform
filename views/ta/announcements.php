<?php $page_title = 'Announcements'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <span>Announcements</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Course Announcements</div>
        <div class="page-subtitle"><?php echo htmlspecialchars($course['title']); ?> &bull; Announcements posted by you are marked "From TA"</div>
    </div>
</div>

<!-- Post New Announcement -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <div class="card-title">Post New Announcement</div>
        <span class="badge badge-ta">From TA</span>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): echo '<div>✕ ' . htmlspecialchars($e) . '</div>'; endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/ta_project/controllers/AnnouncementController.php?course_id=<?php echo $course['id']; ?>"
              onsubmit="return validateAnnouncementForm()">
            <div class="form-group">
                <label for="ann_title">Announcement Title *</label>
                <input type="text" id="ann_title" name="title" maxlength="140"
                       placeholder="e.g. Quiz 2 postponed to next week"
                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                <div class="form-hint">Note: Your announcement will be prefixed with <strong>[From TA]</strong> automatically.</div>
            </div>
            <div class="form-group">
                <label for="ann_body">Message *</label>
                <textarea id="ann_body" name="body" rows="4"
                          placeholder="Write your announcement here..."><?php echo htmlspecialchars($_POST['body'] ?? ''); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Post Announcement</button>
            </div>
        </form>
    </div>
</div>

<!-- Existing Announcements -->
<div class="card">
    <div class="card-header">
        <div class="card-title">All Announcements (<?php echo count($announcements); ?>)</div>
    </div>
    <?php if (empty($announcements)): ?>
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">⚑</div>
            <p>No announcements yet for this course.</p>
        </div>
    </div>
    <?php else: ?>
    <?php foreach ($announcements as $ann): ?>
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
            <div>
                <div style="font-weight:600;font-size:15px;color:var(--navy);margin-bottom:4px;">
                    <?php echo htmlspecialchars($ann['title']); ?>
                </div>
                <div style="font-size:13px;color:var(--muted);margin-bottom:8px;">
                    By <?php echo htmlspecialchars($ann['author_name']); ?>
                    &bull; <?php echo date('d M Y, H:i', strtotime($ann['created_at'])); ?>
                    <?php if ($ann['author_role'] === 'ta'): ?>
                    &bull; <span class="badge badge-ta">From TA</span>
                    <?php endif; ?>
                </div>
                <div style="font-size:14px;color:var(--text);line-height:1.6;">
                    <?php echo nl2br(htmlspecialchars($ann['body'])); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
