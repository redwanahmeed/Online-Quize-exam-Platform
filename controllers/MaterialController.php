<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/MaterialModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id     = current_user_id();
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$action    = $_GET['action'] ?? 'list';
$errors    = [];

if (!$course_id || !model_is_ta_assigned($ta_id, $course_id)) {
    set_flash('error', 'Access denied.');
    redirect('/ta_project/controllers/CourseController.php');
}

$course    = model_get_course_by_id($course_id);
$materials = model_get_course_materials($course_id);

// --- ADD MATERIAL ---
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = sanitize($_POST['title'] ?? '');
    $file_path     = sanitize($_POST['file_path'] ?? '');
    $material_type = sanitize($_POST['material_type'] ?? 'document');

    if (empty($title))     $errors[] = "Title is required.";
    if (strlen($title) > 150) $errors[] = "Title too long.";
    if (empty($file_path)) $errors[] = "File path or link is required.";

    $allowed_types = ['document', 'link', 'video'];
    if (!in_array($material_type, $allowed_types)) $errors[] = "Invalid material type.";

    if (empty($errors)) {
        if (model_create_material($course_id, $ta_id, $title, $file_path, $material_type)) {
            set_flash('success', 'Material uploaded.');
            redirect("/ta_project/controllers/MaterialController.php?course_id={$course_id}");
        } else {
            $errors[] = "Failed to upload material.";
        }
    }
}

// --- EDIT MATERIAL ---
elseif ($action === 'edit') {
    $material_id = isset($_GET['material_id']) ? (int)$_GET['material_id'] : 0;
    $material    = model_get_material_by_id($material_id);

    if (!$material || $material['uploaded_by'] != $ta_id) {
        set_flash('error', 'Not authorized.');
        redirect("/ta_project/controllers/MaterialController.php?course_id={$course_id}");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title         = sanitize($_POST['title'] ?? '');
        $file_path     = sanitize($_POST['file_path'] ?? '');
        $material_type = sanitize($_POST['material_type'] ?? 'document');

        if (empty($title))     $errors[] = "Title is required.";
        if (empty($file_path)) $errors[] = "File path or link is required.";

        if (empty($errors)) {
            if (model_update_material($material_id, $title, $file_path, $material_type)) {
                set_flash('success', 'Material updated.');
                redirect("/ta_project/controllers/MaterialController.php?course_id={$course_id}");
            } else {
                $errors[] = "Failed to update.";
            }
        }
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/material_form.php';
    include __DIR__ . '/../views/layouts/footer.php';
    exit();
}

// --- DELETE MATERIAL ---
elseif ($action === 'delete') {
    $material_id = isset($_GET['material_id']) ? (int)$_GET['material_id'] : 0;
    $material    = model_get_material_by_id($material_id);
    if ($material && $material['uploaded_by'] == $ta_id) {
        model_delete_material($material_id);
        set_flash('success', 'Material deleted.');
    } else {
        set_flash('error', 'Not authorized.');
    }
    redirect("/ta_project/controllers/MaterialController.php?course_id={$course_id}");
}

include __DIR__ . '/../views/layouts/header.php';
include __DIR__ . '/../views/ta/materials.php';
include __DIR__ . '/../views/layouts/footer.php';
?>
