<?php
require_once __DIR__ . '/../config/db.php';

function model_get_user_by_email($email) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? AND is_active = 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $user;
}

function model_get_user_by_id($id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? AND is_active = 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $user;
}

function model_update_profile($id, $name, $phone, $program) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, phone = ?, program = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $program, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_update_password($id, $new_hash) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE users SET password_hash = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_hash, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}
?>
