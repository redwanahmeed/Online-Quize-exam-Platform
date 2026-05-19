<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/AnnouncementModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id     = current_user_id();
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$errors    = [];

if (!$course_id || !model_is_ta_assigned($ta_id, $course_id)) {
    set_flash('error', 'Access denied.');
    redirect('/ta_project/controllers/CourseController.php');
}

$course        = model_get_course_by_id($course_id);
$announcements = model_get_course_announcements($course_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $body  = sanitize($_POST['body'] ?? '');

    if (empty($title)) $errors[] = "Title is required.";
    if (strlen($title) > 150) $errors[] = "Title too long.";
    if (empty($body))  $errors[] = "Body is required.";

    if (empty($errors)) {
        // Prepend [From TA] tag in title
        $tagged_title = "[From TA] " . $title;
        if (model_create_announcement($course_id, $ta_id, $tagged_title, $body)) {
            set_flash('success', 'Announcement posted.');
            redirect("/ta_project/controllers/AnnouncementController.php?course_id={$course_id}");
        } else {
            $errors[] = "Failed to post announcement.";
        }
    }
}

include __DIR__ . '/../views/layouts/header.php';
include __DIR__ . '/../views/ta/announcements.php';
include __DIR__ . '/../views/layouts/footer.php';
?>
