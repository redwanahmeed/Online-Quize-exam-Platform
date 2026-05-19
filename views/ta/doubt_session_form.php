<?php
$is_edit    = isset($session) && $session;
$page_title = $is_edit ? 'Reschedule Session' : 'Schedule Doubt Session';
?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/DoubtSessionController.php">Doubt Sessions</a>
    <span>/</span>
    <span><?php echo $is_edit ? 'Reschedule' : 'New Session'; ?></span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title"><?php echo $is_edit ? 'Reschedule Session' : 'Schedule a Doubt Session'; ?></div>
        <div class="page-subtitle">
            Course: <?php echo htmlspecialchars($course['title']); ?>
            <?php if ($is_edit): ?>
            &bull; <span class="badge badge-warn">Rescheduling will notify all booked students</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-header">
        <div class="card-title">Session Details</div>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): echo '<div>✕ ' . htmlspecialchars($e) . '</div>'; endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
        $form_action = $is_edit
            ? "/ta_project/controllers/DoubtSessionController.php?action=edit&session_id={$session['id']}"
            : "/ta_project/controllers/DoubtSessionController.php?action=create&course_id={$course['id']}";
        ?>
        <form method="POST" action="<?php echo $form_action; ?>" onsubmit="return validateSessionForm()">

            <div class="form-group">
                <label for="session_title">Session Title *</label>
                <input type="text" id="session_title" name="title" maxlength="150" required
                       placeholder="e.g. Midterm Doubt Clearing — Algorithms"
                       value="<?php echo htmlspecialchars($is_edit ? ($session['title'] ?? '') : ($_POST['title'] ?? '')); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="scheduled_at">Date &amp; Time *</label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at" required
                           value="<?php
                           if ($is_edit && $session['scheduled_at']) {
                               echo date('Y-m-d\TH:i', strtotime($session['scheduled_at']));
                           } else {
                               echo $_POST['scheduled_at'] ?? '';
                           }
                           ?>">
                </div>
                <div class="form-group">
                    <label for="duration_minutes">Duration (minutes) *</label>
                    <input type="number" id="duration_minutes" name="duration_minutes" min="1" required
                           value="<?php echo $is_edit ? $session['duration_minutes'] : ($_POST['duration_minutes'] ?? 60); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="location_or_link">Location or Meeting Link</label>
                    <input type="text" id="location_or_link" name="location_or_link"
                           placeholder="e.g. Room 204 or https://meet.google.com/abc"
                           value="<?php echo htmlspecialchars($is_edit ? ($session['location_or_link'] ?? '') : ($_POST['location_or_link'] ?? '')); ?>">
                </div>
                <div class="form-group">
                    <label for="max_attendees">Max Attendees *</label>
                    <input type="number" id="max_attendees" name="max_attendees" min="1" required
                           value="<?php echo $is_edit ? $session['max_attendees'] : ($_POST['max_attendees'] ?? 20); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">
                    <?php echo $is_edit ? '💾 Save Changes' : '📅 Schedule Session'; ?>
                </button>
                <a href="/ta_project/controllers/DoubtSessionController.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
