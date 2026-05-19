<?php
require_once __DIR__ . '/../config/db.php';

function model_get_course_quizzes($course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT q.*, u.name AS created_by_name,
               (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count,
               (SELECT COUNT(*) FROM attempts WHERE quiz_id = q.id) AS attempt_count
        FROM quizzes q
        JOIN users u ON q.created_by = u.id
        WHERE q.course_id = ?
        ORDER BY q.id DESC
    ");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $quizzes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $quizzes[] = $row;
    }
    mysqli_close($conn);
    return $quizzes;
}

function model_get_quiz_by_id($quiz_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT * FROM quizzes WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $quiz_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $quiz = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $quiz;
}

function model_create_quiz($course_id, $created_by, $title, $description, $time_limit, $total_marks, $pass_mark, $available_from, $available_until) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        INSERT INTO quizzes (course_id, created_by, title, description, time_limit_minutes, total_marks, pass_mark, quiz_type, status, available_from, available_until)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'practice', 'draft', ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, "iissiidss", $course_id, $created_by, $title, $description, $time_limit, $total_marks, $pass_mark, $available_from, $available_until);
    $ok = mysqli_stmt_execute($stmt);
    $new_id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return $ok ? $new_id : false;
}

function model_update_quiz($quiz_id, $title, $description, $time_limit, $total_marks, $pass_mark, $available_from, $available_until) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        UPDATE quizzes SET title=?, description=?, time_limit_minutes=?, total_marks=?, pass_mark=?, available_from=?, available_until=?
        WHERE id = ? AND quiz_type = 'practice'
    ");
    mysqli_stmt_bind_param($stmt, "ssiiddsi", $title, $description, $time_limit, $total_marks, $pass_mark, $available_from, $available_until, $quiz_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_delete_quiz($quiz_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "DELETE FROM quizzes WHERE id = ? AND quiz_type = 'practice'");
    mysqli_stmt_bind_param($stmt, "i", $quiz_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

// Questions
function model_get_quiz_questions($quiz_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index ASC, id ASC");
    mysqli_stmt_bind_param($stmt, "i", $quiz_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $questions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = $row;
    }
    mysqli_close($conn);
    return $questions;
}

function model_get_question_by_id($question_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT * FROM questions WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $question_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $q = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $q;
}

function model_add_question($quiz_id, $question_text, $marks, $order_index, $created_by) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "INSERT INTO questions (quiz_id, question_text, marks, order_index, created_by) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isdii", $quiz_id, $question_text, $marks, $order_index, $created_by);
    $ok = mysqli_stmt_execute($stmt);
    $new_id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return $ok ? $new_id : false;
}

function model_update_question($question_id, $question_text, $marks, $order_index) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE questions SET question_text=?, marks=?, order_index=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sdii", $question_text, $marks, $order_index, $question_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_delete_question($question_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "DELETE FROM questions WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $question_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

// Options
function model_get_question_options($question_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT * FROM options WHERE question_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $question_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $options = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row;
    }
    mysqli_close($conn);
    return $options;
}

function model_delete_options_for_question($question_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "DELETE FROM options WHERE question_id=?");
    mysqli_stmt_bind_param($stmt, "i", $question_id);
    mysqli_stmt_execute($stmt);
    mysqli_close($conn);
}

function model_add_option($question_id, $option_text, $is_correct) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isi", $question_id, $option_text, $is_correct);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}
?>
