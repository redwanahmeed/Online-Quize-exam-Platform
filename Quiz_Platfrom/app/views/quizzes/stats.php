<?php require_once "../app/views/layouts/header.php"; ?>
<h2>Quiz Statistics: <?php echo htmlspecialchars($quiz['title']); ?></h2>

<div class="stats-container" id="quiz-stats">
    <div class="loading">Loading statistics...</div>
</div>

<div class="attempts-list">
    <h3>Student Attempts</h3>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Score</th>
                <th>Duration (mins)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attempts as $attempt): ?>
            <tr>
                <td><?php echo htmlspecialchars($attempt['student_name']); ?></td>
                <td><?php echo $attempt['score']; ?>/<?php echo $quiz['total_marks']; ?></td>
                <td><?php echo $attempt['duration']; ?></td>
                <td><?php echo $attempt['passed'] ? 'Passed' : 'Failed'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// AJAX request to fetch quiz statistics
function loadQuizStats() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?controller=quiz&action=getStats&quiz_id=<?php echo $quiz['id']; ?>', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                var stats = response.data;
                var html = '<div class="stat-card">' +
                          '<h3>Total Attempts</h3>' +
                          '<p class="stat-number">' + stats.total_attempts + '</p>' +
                          '</div>' +
                          '<div class="stat-card">' +
                          '<h3>Average Score</h3>' +
                          '<p class="stat-number">' + Math.round(stats.average_score) + '</p>' +
                          '</div>' +
                          '<div class="stat-card">' +
                          '<h3>Highest Score</h3>' +
                          '<p class="stat-number">' + stats.highest_score + '</p>' +
                          '</div>' +
                          '<div class="stat-card">' +
                          '<h3>Pass Rate</h3>' +
                          '<p class="stat-number">' + Math.round(stats.pass_rate) + '%</p>' +
                          '</div>';
                document.getElementById('quiz-stats').innerHTML = html;
            }
        }
    };
    xhr.send();
}

// Load stats when page loads
loadQuizStats();
</script>
<?php require_once "../app/views/layouts/footer.php"; ?>