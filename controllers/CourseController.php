<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/QuizModel.php';
require_once __DIR__ . '/../models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id = current_user_id();
$action = $_GET['action'] ?? 'list';
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($action === 'view' && $course_id) {
    // Verify TA is assigned to this course
    if (!model_is_ta_assigned($ta_id, $course_id)) {
        set_flash('error', 'You are not assigned to this course.');
        redirect('/ta_project/controllers/CourseController.php');
    }
    $course   = model_get_course_by_id($course_id);
    $students = model_get_enrolled_students($course_id);
    $quizzes  = model_get_course_quizzes($course_id);
    $summary  = model_get_course_summary($course_id);

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/course_detail.php';
    include __DIR__ . '/../views/layouts/footer.php';
} else {
    $courses = model_get_ta_courses($ta_id);
    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/courses.php';
    include __DIR__ . '/../views/layouts/footer.php';
}
?>
