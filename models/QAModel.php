<?php
require_once __DIR__ . '/../config/db.php';

function model_get_course_qa_questions($course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT q.*, u.name AS student_name,
               (SELECT COUNT(*) FROM qa_answers a WHERE a.qa_question_id = q.id) AS answer_count
        FROM qa_questions q
        JOIN users u ON q.student_id = u.id
        WHERE q.course_id = ?
        ORDER BY q.created_at DESC
    ");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    mysqli_close($conn);
    return $list;
}

function model_get_qa_question_by_id($qa_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT q.*, u.name AS student_name
        FROM qa_questions q
        JOIN users u ON q.student_id = u.id
        WHERE q.id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $qa_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $q = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $q;
}

function model_get_qa_answers($qa_question_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT a.*, u.name AS author_name, u.role AS author_role
        FROM qa_answers a
        JOIN users u ON a.author_id = u.id
        WHERE a.qa_question_id = ?
        ORDER BY a.is_endorsed DESC, a.created_at ASC
    ");
    mysqli_stmt_bind_param($stmt, "i", $qa_question_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    mysqli_close($conn);
    return $list;
}

function model_post_qa_answer($qa_question_id, $author_id, $body) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "INSERT INTO qa_answers (qa_question_id, author_id, body) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iis", $qa_question_id, $author_id, $body);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_endorse_answer($answer_id, $endorse) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE qa_answers SET is_endorsed = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $endorse, $answer_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_mark_qa_resolved($qa_id, $resolved) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE qa_questions SET is_resolved = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $resolved, $qa_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}
?>
