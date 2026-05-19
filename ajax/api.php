<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/QAModel.php';
require_once __DIR__ . '/../models/AttemptModel.php';
require_once __DIR__ . '/../models/CourseModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!is_logged_in() || current_role() !== 'ta') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$ta_id  = current_user_id();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- ENDORSE / UNENDORSE ANSWER ---
if ($action === 'toggle_endorse') {
    $answer_id = isset($_POST['answer_id']) ? (int)$_POST['answer_id'] : 0;
    $endorse   = isset($_POST['endorse']) ? (int)$_POST['endorse'] : 1;

    if (!$answer_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid answer ID']);
        exit();
    }

    $ok = model_endorse_answer($answer_id, $endorse);
    echo json_encode([
        'success'  => $ok,
        'endorsed' => (bool)$endorse,
        'message'  => $ok ? 'Updated' : 'Failed'
    ]);
    exit();
}

// --- GET AT-RISK STUDENTS (AJAX filter) ---
if ($action === 'get_at_risk') {
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    $threshold = isset($_GET['threshold']) ? (float)$_GET['threshold'] : 50;

    if (!$course_id || !model_is_ta_assigned($ta_id, $course_id)) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit();
    }

    $threshold = max(0, min(100, $threshold));
    $students  = model_get_at_risk_students($course_id, $threshold);

    echo json_encode([
        'success'   => true,
        'students'  => $students,
        'threshold' => $threshold,
        'count'     => count($students)
    ]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
?>
