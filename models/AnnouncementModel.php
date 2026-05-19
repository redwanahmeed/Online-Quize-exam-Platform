<?php
require_once __DIR__ . '/../config/db.php';

function model_get_course_announcements($course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT a.*, u.name AS author_name, u.role AS author_role
        FROM announcements a
        JOIN users u ON a.author_id = u.id
        WHERE a.course_id = ?
        ORDER BY a.created_at DESC
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

function model_create_announcement($course_id, $author_id, $title, $body) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "INSERT INTO announcements (course_id, author_id, title, body) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiss", $course_id, $author_id, $title, $body);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}
?>
