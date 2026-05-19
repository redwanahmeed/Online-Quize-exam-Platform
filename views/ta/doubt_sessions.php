<?php $page_title = 'Doubt Sessions'; ?>
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Doubt Sessions</div>
        <div class="page-subtitle">Sessions you have scheduled across all your courses</div>
    </div>
    <div class="page-header-actions">
        <?php
        // Get TA's courses for quick-create dropdown
        require_once __DIR__ . '/../../models/CourseModel.php';
        $ta_courses = model_get_ta_courses(current_user_id());
        if (!empty($ta_courses)):
        ?>
        <div style="display:flex;gap:8px;align-items:center;">
            <select id="create-course-select" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;font-family:inherit;">
                <option value="">-- Select Course --</option>
                <?php foreach ($ta_courses as $tc): ?>
                <option value="<?php echo $tc['id']; ?>"><?php echo htmlspecialchars($tc['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-accent" onclick="
                var cid = document.getElementById('create-course-select').value;
                if(cid) window.location='/ta_project/controllers/DoubtSessionController.php?action=create&course_id='+cid;
                else alert('Please select a course first.');
            ">+ Schedule Session</button>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($sessions)): ?>
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">◷</div>
            <p>No doubt sessions scheduled yet. Use the button above to create your first session.</p>
        </div>
    </div>
</div>
<?php else: ?>

<?php foreach ($sessions as $s):
    $dt        = strtotime($s['scheduled_at']);
    $is_past   = $dt < time();
?>
<div class="session-card" style="<?php echo $is_past ? 'opacity:.75;' : ''; ?>">
    <div class="session-date" style="<?php echo $is_past ? 'background:var(--muted);' : ''; ?>">
        <div class="day"><?php echo date('d', $dt); ?></div>
        <div class="month"><?php echo date('M', $dt); ?></div>
        <div style="font-size:10px;margin-top:2px;color:rgba(255,255,255,.6);"><?php echo date('Y', $dt); ?></div>
    </div>
    <div class="session-info">
        <div class="session-title"><?php echo htmlspecialchars($s['title']); ?></div>
        <div class="session-meta">
            📚 <?php echo htmlspecialchars($s['course_title']); ?>
            &bull; ⏰ <?php echo date('H:i', $dt); ?>
            &bull; ⏱ <?php echo $s['duration_minutes']; ?> min
            &bull; 👥 <?php echo $s['booking_count']; ?> / <?php echo $s['max_attendees']; ?> booked
            <?php if ($s['location_or_link']): ?>
            &bull; 📍 <?php echo htmlspecialchars($s['location_or_link']); ?>
            <?php endif; ?>
        </div>
        <?php if ($is_past): ?>
        <span class="badge badge-grey" style="margin-bottom:8px;">Past Session</span>
        <?php else: ?>
        <span class="badge badge-green" style="margin-bottom:8px;">Upcoming</span>
        <?php endif; ?>
        <div class="session-actions">
            <a href="/ta_project/controllers/DoubtSessionController.php?action=bookings&session_id=<?php echo $s['id']; ?>" class="btn btn-primary btn-sm">View Bookings (<?php echo $s['booking_count']; ?>)</a>
            <a href="/ta_project/controllers/DoubtSessionController.php?action=edit&session_id=<?php echo $s['id']; ?>" class="btn btn-outline btn-sm">Reschedule</a>
            <a href="/ta_project/controllers/DoubtSessionController.php?action=delete&session_id=<?php echo $s['id']; ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Cancel this session? All booked students will be notified.')">Cancel</a>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
