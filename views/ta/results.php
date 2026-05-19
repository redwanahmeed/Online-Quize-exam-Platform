<?php $page_title = 'Student Results'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <span>Results</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Student Results</div>
        <div class="page-subtitle"><?php echo htmlspecialchars($course['title']); ?> &bull; <?php echo count($attempts); ?> attempt(s) recorded</div>
    </div>
    <div class="page-header-actions">
        <a href="/ta_project/controllers/ResultsController.php?action=at_risk&course_id=<?php echo $course['id']; ?>" class="btn btn-danger">⚑ At-Risk Students</a>
    </div>
</div>

<?php if (empty($attempts)): ?>
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">▤</div>
            <p>No quiz attempts recorded yet for this course.</p>
        </div>
    </div>
</div>
<?php else: ?>

<!-- Summary Stats -->
<?php
$total_attempts = count($attempts);
$graded = array_filter($attempts, function($a) { return $a['is_graded']; });
$scores = array_column(array_filter($graded, function($a){ return $a['score'] !== null; }), 'score');
$avg_score = count($scores) ? round(array_sum($scores) / count($scores), 1) : 0;
$passed = 0;
foreach ($graded as $a) {
    if ($a['pass_mark'] && $a['score'] >= $a['pass_mark']) $passed++;
}
?>
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon blue">▤</div>
        <div><div class="stat-label">Total Attempts</div><div class="stat-value"><?php echo $total_attempts; ?></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✓</div>
        <div><div class="stat-label">Graded</div><div class="stat-value"><?php echo count($graded); ?></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold">%</div>
        <div><div class="stat-label">Average Score</div><div class="stat-value"><?php echo $avg_score; ?>%</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">★</div>
        <div><div class="stat-label">Passed</div><div class="stat-value"><?php echo $passed; ?></div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">All Attempts</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Quiz</th>
                    <th>Score</th>
                    <th>Pass Mark</th>
                    <th>Status</th>
                    <th>Started</th>
                    <th>Completed</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attempts as $i => $a): ?>
                <tr>
                    <td class="text-muted"><?php echo $i + 1; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($a['student_name']); ?></strong>
                        <br><span class="text-small text-muted"><?php echo htmlspecialchars($a['student_no'] ?? ''); ?></span>
                    </td>
                    <td class="text-small"><?php echo htmlspecialchars($a['quiz_title']); ?></td>
                    <td>
                        <?php if ($a['score'] !== null): ?>
                        <strong><?php echo number_format($a['score'], 1); ?></strong>
                        <span class="text-muted text-small">/ <?php echo $a['total_marks']; ?></span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-small"><?php echo $a['pass_mark'] ?? '—'; ?></td>
                    <td>
                        <?php if ($a['is_graded']): ?>
                        <span class="badge badge-green">Graded</span>
                        <?php else: ?>
                        <span class="badge badge-grey">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-small"><?php echo $a['started_at'] ? date('d M Y H:i', strtotime($a['started_at'])) : '—'; ?></td>
                    <td class="text-small"><?php echo $a['completed_at'] ? date('d M Y H:i', strtotime($a['completed_at'])) : '—'; ?></td>
                    <td>
                        <?php if ($a['is_graded'] && $a['score'] !== null && $a['pass_mark']): ?>
                            <?php if ($a['score'] >= $a['pass_mark']): ?>
                            <span class="badge badge-green">Pass</span>
                            <?php else: ?>
                            <span class="badge badge-red">Fail</span>
                            <?php endif; ?>
                        <?php else: ?>
                        <span class="badge badge-grey">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
