<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/AttemptModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id     = current_user_id();
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$action    = $_GET['action'] ?? 'results';

if (!$course_id || !model_is_ta_assigned($ta_id, $course_id)) {
    set_flash('error', 'Access denied.');
    redirect('/ta_project/controllers/CourseController.php');
}

$course = model_get_course_by_id($course_id);

if ($action === 'at_risk') {
    $threshold = isset($_GET['threshold']) ? (float)$_GET['threshold'] : 50;
    if ($threshold < 0) $threshold = 0;
    if ($threshold > 100) $threshold = 100;
    $at_risk_students = model_get_at_risk_students($course_id, $threshold);

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/at_risk.php';
    include __DIR__ . '/../views/layouts/footer.php';
} else {
    $attempts = model_get_course_attempts($course_id);

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/results.php';
    include __DIR__ . '/../views/layouts/footer.php';
}
?>
