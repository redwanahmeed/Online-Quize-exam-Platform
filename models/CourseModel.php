<?php
require_once __DIR__ . '/../config/db.php';

function model_get_ta_courses($ta_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT c.*, u.name AS instructor_name, s.name AS subject_name,
               (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.status = 'active') AS student_count
        FROM course_tas ct
        JOIN courses c ON ct.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        JOIN subjects s ON c.subject_id = s.id
        WHERE ct.ta_id = ?
        ORDER BY c.created_at DESC
    ");
    mysqli_stmt_bind_param($stmt, "i", $ta_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    mysqli_close($conn);
    return $courses;
}

function model_get_course_by_id($course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT c.*, u.name AS instructor_name, s.name AS subject_name
        FROM courses c
        JOIN users u ON c.instructor_id = u.id
        JOIN subjects s ON c.subject_id = s.id
        WHERE c.id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $course = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $course;
}

function model_is_ta_assigned($ta_id, $course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT id FROM course_tas WHERE ta_id = ? AND course_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $ta_id, $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $found = mysqli_fetch_assoc($result) ? true : false;
    mysqli_close($conn);
    return $found;
}

function model_get_enrolled_students($course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT u.id, u.name, u.email, u.student_id, u.program, e.enrolled_at, e.status
        FROM enrollments e
        JOIN users u ON e.student_id = u.id
        WHERE e.course_id = ? AND e.status = 'active'
        ORDER BY u.name ASC
    ");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    mysqli_close($conn);
    return $students;
}

function model_get_course_summary($course_id) {
    $conn = get_db();

    // Total active students
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM enrollments WHERE course_id = ? AND status = 'active'");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $total_students = mysqli_fetch_assoc($r)['total'];

    // Total quizzes
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM quizzes WHERE course_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $total_quizzes = mysqli_fetch_assoc($r)['total'];

    // Total attempts
    $stmt = mysqli_prepare($conn, "
        SELECT COUNT(*) AS total, AVG(a.score) AS avg_score
        FROM attempts a
        JOIN quizzes q ON a.quiz_id = q.id
        WHERE q.course_id = ? AND a.is_graded = 1
    ");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    $attempt_data = mysqli_fetch_assoc($r);

    mysqli_close($conn);
    return [
        'total_students' => $total_students,
        'total_quizzes'  => $total_quizzes,
        'total_attempts' => $attempt_data['total'],
        'avg_score'      => round($attempt_data['avg_score'] ?? 0, 2)
    ];
}
?>
