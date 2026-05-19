<?php
require_once __DIR__ . '/../config/db.php';

function model_get_course_materials($course_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "
        SELECT m.*, u.name AS uploader_name
        FROM course_materials m
        JOIN users u ON m.uploaded_by = u.id
        WHERE m.course_id = ?
        ORDER BY m.created_at DESC
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

function model_get_material_by_id($material_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT * FROM course_materials WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $material_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $m = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $m;
}

function model_create_material($course_id, $uploaded_by, $title, $file_path, $material_type) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "INSERT INTO course_materials (course_id, uploaded_by, title, file_path, material_type) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisss", $course_id, $uploaded_by, $title, $file_path, $material_type);
    $ok = mysqli_stmt_execute($stmt);
    $new_id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return $ok ? $new_id : false;
}

function model_update_material($material_id, $title, $file_path, $material_type) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE course_materials SET title=?, file_path=?, material_type=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $title, $file_path, $material_type, $material_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}

function model_delete_material($material_id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "DELETE FROM course_materials WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $material_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    return $ok;
}
?>
