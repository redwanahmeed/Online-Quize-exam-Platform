<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/QAModel.php';

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

$course = model_get_course_by_id($course_id);

// --- VIEW SINGLE Q&A THREAD ---
if ($action === 'view') {
    $qa_id   = isset($_GET['qa_id']) ? (int)$_GET['qa_id'] : 0;
    $question = model_get_qa_question_by_id($qa_id);
    $answers  = model_get_qa_answers($qa_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_answer'])) {
        $body = sanitize($_POST['body'] ?? '');
        if (empty($body)) {
            $errors[] = "Answer cannot be empty.";
        } else {
            model_post_qa_answer($qa_id, $ta_id, $body);
            set_flash('success', 'Answer posted.');
            redirect("/ta_project/controllers/QAController.php?course_id={$course_id}&action=view&qa_id={$qa_id}");
        }
    }

    if (isset($_GET['resolve'])) {
        $resolved = (int)$_GET['resolve'];
        model_mark_qa_resolved($qa_id, $resolved);
        redirect("/ta_project/controllers/QAController.php?course_id={$course_id}&action=view&qa_id={$qa_id}");
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/qa_thread.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- ENDORSE ANSWER (AJAX handled separately, also GET fallback) ---
elseif ($action === 'endorse') {
    $answer_id = isset($_GET['answer_id']) ? (int)$_GET['answer_id'] : 0;
    $qa_id     = isset($_GET['qa_id']) ? (int)$_GET['qa_id'] : 0;
    $endorse   = isset($_GET['endorse']) ? (int)$_GET['endorse'] : 1;
    model_endorse_answer($answer_id, $endorse);
    redirect("/ta_project/controllers/QAController.php?course_id={$course_id}&action=view&qa_id={$qa_id}");
}

// --- LIST ALL Q&A ---
else {
    $qa_questions = model_get_course_qa_questions($course_id);
    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/qa_list.php';
    include __DIR__ . '/../views/layouts/footer.php';
}
?>
