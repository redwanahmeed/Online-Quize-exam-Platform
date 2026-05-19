<?php
require_once __DIR__ . '/../config/db.php';

function model_get_ta_doubt_sessions($ta_id, $course_id = null) {
    $conn = get_db();
    if ($course_id) {
        $stmt = mysqli_prepare($conn, "
            SELECT ds.*, c.title AS course_title,
                   (SELECT COUNT(*) FROM doubt_session_bookings b WHERE b.doubt_session_id = ds.id) AS booking_count
            FROM doubt_sessions ds
            JOIN courses c ON ds.course_id = c.id
            WHERE ds.ta_id = ? AND ds.course_id = ?
            ORDER BY ds.scheduled_at DESC
        ");
        mysqli_stmt_bind_param($stmt, "ii", $ta_id, $course_id);
    } else {
        $stmt = mysqli_prepare($conn, "
            SELECT ds.*, c.title AS course_title,
                   (SELECT COUNT(*) FROM doubt_session_bookings b WHERE b.doubt_session_id = ds.id) AS booking_count
            FROM doubt_sessions ds
            JOIN courses c ON ds.course_id = c.id
            WHERE ds.ta_id = ?
            ORDER BY ds.scheduled_at DESC
        ");
        mysqli_stmt_bind_param($stmt, "i", $ta_id);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    mysqli_close($conn);
    return $list;
}

function model_get_doubt_session_by_id($session_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT ds.*, c.title AS course_title FROM doubt_sessions ds JOIN courses c ON ds.course_id = c.id WHERE ds.id = ?");
    mysqli_stmt_bind_param($stmt, "i", $session_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $s = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $s;
}

function model_create_doubt_session($course_id, $ta_id, $title, $scheduled_at, $duration, $location, $max_attendees) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "INSERT INTO doubt_sessions (course_id, ta_id, title, scheduled_at, duration_minutes, location_or_link, max_attendees) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iissisi", $course_id, $ta_id, $title, $scheduled_at, $duration, $location, $max_attendees);
    $ok = mysqli_stmt_execute($stmt);
    $new_id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return $ok ? $new_id : false;
}

function model_update_doubt_session($session_id, $title, $scheduled_at, $duration, $location, $max_attendees) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE doubt_sessions SET title=?, scheduled_at=?, duration_minutes=?, location_or_link=?, max_attendees=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssisii", $title, $scheduled_at, $duration, $location, $max_attendees, $session_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_delete_doubt_session($session_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "DELETE FROM doubt_sessions WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $session_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_get_session_bookings($session_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT b.*, u.name AS student_name, u.email, u.student_id AS student_no
        FROM doubt_session_bookings b
        JOIN users u ON b.student_id = u.id
        WHERE b.doubt_session_id = ?
        ORDER BY b.booked_at ASC
    ");
    mysqli_stmt_bind_param($stmt, "i", $session_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    mysqli_close($conn);
    return $list;
}

function model_get_booked_student_emails($session_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT u.email, u.name FROM doubt_session_bookings b JOIN users u ON b.student_id = u.id WHERE b.doubt_session_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $session_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    mysqli_close($conn);
    return $list;
}
?>
