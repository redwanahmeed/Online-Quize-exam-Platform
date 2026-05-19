<?php
require_once __DIR__ . '/../config/db.php';

function model_get_course_attempts($course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT a.*, u.name AS student_name, u.student_id AS student_no, u.email,
               q.title AS quiz_title, q.pass_mark, q.total_marks
        FROM attempts a
        JOIN users u ON a.student_id = u.id
        JOIN quizzes q ON a.quiz_id = q.id
        WHERE q.course_id = ?
        ORDER BY a.started_at DESC
    ");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $attempts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $attempts[] = $row;
    }
    mysqli_close($conn);
    return $attempts;
}

function model_get_at_risk_students($course_id, $threshold) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT u.id, u.name, u.email, u.student_id AS student_no, u.program,
               COUNT(a.id) AS attempt_count,
               AVG(a.score) AS avg_score,
               MIN(a.score) AS min_score
        FROM attempts a
        JOIN users u ON a.student_id = u.id
        JOIN quizzes q ON a.quiz_id = q.id
        WHERE q.course_id = ? AND a.is_graded = 1
        GROUP BY u.id, u.name, u.email, u.student_id, u.program
        HAVING avg_score < ?
        ORDER BY avg_score ASC
    ");
    mysqli_stmt_bind_param($stmt, "id", $course_id, $threshold);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    mysqli_close($conn);
    return $students;
}
?>
