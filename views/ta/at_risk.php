<?php $page_title = 'At-Risk Students'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <span>At-Risk Students</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">⚑ At-Risk Students</div>
        <div class="page-subtitle">
            <?php echo htmlspecialchars($course['title']); ?> &bull;
            Students whose average score falls below a configurable threshold
        </div>
    </div>
    <div class="page-header-actions">
        <a href="/ta_project/controllers/ResultsController.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline">← All Results</a>
    </div>
</div>

<div class="alert alert-info">
    ℹ Adjust the threshold slider below to filter at-risk students. Results update automatically using AJAX without reloading the page.
</div>

<!-- AJAX Threshold Control -->
<div class="threshold-control">
    <label for="threshold-slider">Risk Threshold:</label>
    <input type="range" id="threshold-slider" min="0" max="100" value="<?php echo $threshold; ?>" step="5">
    <span id="threshold-value"><?php echo $threshold; ?>%</span>
    <span class="text-muted text-small">Students averaging below this score are flagged at-risk</span>
</div>

<!-- Hidden course ID for AJAX -->
<input type="hidden" id="course-id-input" value="<?php echo $course['id']; ?>">

<!-- Results container updated by AJAX -->
<div id="at-risk-results">
    <div class="empty-state">
        <div class="empty-icon" style="font-size:24px;">⏳</div>
        <p>Loading at-risk data...</p>
    </div>
</div>

<div style="margin-top:20px;">
    <div class="alert alert-warn">
        ⚑ <strong>Flagging Note:</strong> After identifying at-risk students, contact the instructor to review these students. You may also schedule a targeted doubt session or reach out via course announcements.
    </div>
</div>
