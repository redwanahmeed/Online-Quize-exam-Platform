<?php
$is_edit    = isset($quiz) && $quiz;
$page_title = $is_edit ? 'Edit Quiz' : 'Create Practice Quiz';
?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <span><?php echo $is_edit ? 'Edit Quiz' : 'New Quiz'; ?></span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title"><?php echo $is_edit ? 'Edit Practice Quiz' : 'Create Practice Quiz'; ?></div>
        <div class="page-subtitle">
            Course: <?php echo htmlspecialchars($course['title']); ?>
            &bull; Quizzes require instructor approval before students can see them.
        </div>
    </div>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header">
        <div class="card-title">Quiz Details</div>
        <span class="badge badge-gold">Practice Quiz</span>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
            <div>✕ <?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateQuizForm()">
            <div class="form-group">
                <label for="title">Quiz Title *</label>
                <input type="text" id="title" name="title" maxlength="150" required
                       value="<?php echo htmlspecialchars($is_edit ? $quiz['title'] : ($_POST['title'] ?? '')); ?>"
                       placeholder="e.g. Week 3 Practice — Recursion">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Optional description for students..."><?php echo htmlspecialchars($is_edit ? $quiz['description'] : ($_POST['description'] ?? '')); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="time_limit_minutes">Time Limit (minutes) *</label>
                    <input type="number" id="time_limit_minutes" name="time_limit_minutes" min="1" required
                           value="<?php echo $is_edit ? $quiz['time_limit_minutes'] : ($_POST['time_limit_minutes'] ?? 30); ?>">
                </div>
                <div class="form-group">
                    <label for="total_marks">Total Marks *</label>
                    <input type="number" id="total_marks" name="total_marks" min="1" step="0.5" required
                           value="<?php echo $is_edit ? $quiz['total_marks'] : ($_POST['total_marks'] ?? 100); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="pass_mark">Pass Mark</label>
                    <input type="number" id="pass_mark" name="pass_mark" min="0" step="0.5"
                           value="<?php echo $is_edit ? $quiz['pass_mark'] : ($_POST['pass_mark'] ?? 50); ?>">
                </div>
                <div class="form-group">
                    <label>Quiz Type</label>
                    <input type="text" value="Practice (Pending Approval)" readonly style="background:#f4f6fb;cursor:not-allowed">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="available_from">Available From</label>
                    <input type="datetime-local" id="available_from" name="available_from"
                           value="<?php echo $is_edit ? ($quiz['available_from'] ? date('Y-m-d\TH:i', strtotime($quiz['available_from'])) : '') : ($_POST['available_from'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="available_until">Available Until</label>
                    <input type="datetime-local" id="available_until" name="available_until"
                           value="<?php echo $is_edit ? ($quiz['available_until'] ? date('Y-m-d\TH:i', strtotime($quiz['available_until'])) : '') : ($_POST['available_until'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent"><?php echo $is_edit ? 'Save Changes' : 'Create Quiz'; ?></button>
                <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
