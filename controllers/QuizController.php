<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/QuizModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id  = current_user_id();
$action = $_GET['action'] ?? 'list';
$errors = [];
$success = '';

// Helper: verify TA assigned to course
function verify_course_access($ta_id, $course_id) {
    if (!$course_id || !model_is_ta_assigned($ta_id, $course_id)) {
        set_flash('error', 'Access denied.');
        redirect('/ta_project/controllers/CourseController.php');
    }
}

// --- CREATE QUIZ ---
if ($action === 'create') {
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    verify_course_access($ta_id, $course_id);
    $course = model_get_course_by_id($course_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title          = sanitize($_POST['title'] ?? '');
        $description    = sanitize($_POST['description'] ?? '');
        $time_limit     = (int)($_POST['time_limit_minutes'] ?? 0);
        $total_marks    = (float)($_POST['total_marks'] ?? 0);
        $pass_mark      = (float)($_POST['pass_mark'] ?? 0);
        $available_from = sanitize($_POST['available_from'] ?? '');
        $available_until= sanitize($_POST['available_until'] ?? '');

        if (empty($title))      $errors[] = "Title is required.";
        if (strlen($title) > 150) $errors[] = "Title too long.";
        if ($time_limit < 1)    $errors[] = "Time limit must be at least 1 minute.";
        if ($total_marks <= 0)  $errors[] = "Total marks must be positive.";
        if ($pass_mark < 0 || $pass_mark > $total_marks) $errors[] = "Pass mark invalid.";

        if (empty($errors)) {
            $quiz_id = model_create_quiz($course_id, $ta_id, $title, $description, $time_limit, $total_marks, $pass_mark, $available_from, $available_until);
            if ($quiz_id) {
                set_flash('success', 'Practice quiz created! Pending instructor approval.');
                redirect("/ta_project/controllers/QuizController.php?action=questions&quiz_id={$quiz_id}&course_id={$course_id}");
            } else {
                $errors[] = "Failed to create quiz.";
            }
        }
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/quiz_form.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- EDIT QUIZ ---
elseif ($action === 'edit') {
    $quiz_id   = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    verify_course_access($ta_id, $course_id);
    $course = model_get_course_by_id($course_id);
    $quiz   = model_get_quiz_by_id($quiz_id);

    if (!$quiz || $quiz['course_id'] != $course_id || $quiz['quiz_type'] !== 'practice') {
        set_flash('error', 'Quiz not found or not editable.');
        redirect("/ta_project/controllers/CourseController.php?action=view&course_id={$course_id}");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title          = sanitize($_POST['title'] ?? '');
        $description    = sanitize($_POST['description'] ?? '');
        $time_limit     = (int)($_POST['time_limit_minutes'] ?? 0);
        $total_marks    = (float)($_POST['total_marks'] ?? 0);
        $pass_mark      = (float)($_POST['pass_mark'] ?? 0);
        $available_from = sanitize($_POST['available_from'] ?? '');
        $available_until= sanitize($_POST['available_until'] ?? '');

        if (empty($title))     $errors[] = "Title is required.";
        if ($time_limit < 1)   $errors[] = "Time limit must be at least 1 minute.";
        if ($total_marks <= 0) $errors[] = "Total marks must be positive.";

        if (empty($errors)) {
            if (model_update_quiz($quiz_id, $title, $description, $time_limit, $total_marks, $pass_mark, $available_from, $available_until)) {
                set_flash('success', 'Quiz updated successfully.');
                redirect("/ta_project/controllers/QuizController.php?action=questions&quiz_id={$quiz_id}&course_id={$course_id}");
            } else {
                $errors[] = "Failed to update quiz.";
            }
        }
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/quiz_form.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- DELETE QUIZ ---
elseif ($action === 'delete') {
    $quiz_id   = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    verify_course_access($ta_id, $course_id);
    $quiz = model_get_quiz_by_id($quiz_id);
    if ($quiz && $quiz['course_id'] == $course_id && $quiz['quiz_type'] === 'practice') {
        model_delete_quiz($quiz_id);
        set_flash('success', 'Quiz deleted.');
    }
    redirect("/ta_project/controllers/CourseController.php?action=view&course_id={$course_id}");
}

// --- MANAGE QUESTIONS ---
elseif ($action === 'questions') {
    $quiz_id   = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    verify_course_access($ta_id, $course_id);
    $course    = model_get_course_by_id($course_id);
    $quiz      = model_get_quiz_by_id($quiz_id);

    if (!$quiz || $quiz['course_id'] != $course_id) {
        set_flash('error', 'Quiz not found.');
        redirect("/ta_project/controllers/CourseController.php?action=view&course_id={$course_id}");
    }

    $questions = model_get_quiz_questions($quiz_id);
    foreach ($questions as &$q) {
        $q['options'] = model_get_question_options($q['id']);
    }
    unset($q);

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/quiz_questions.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- ADD QUESTION ---
elseif ($action === 'add_question') {
    $quiz_id   = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    verify_course_access($ta_id, $course_id);
    $course = model_get_course_by_id($course_id);
    $quiz   = model_get_quiz_by_id($quiz_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $qtext       = sanitize($_POST['question_text'] ?? '');
        $marks       = (float)($_POST['marks'] ?? 1);
        $order_index = (int)($_POST['order_index'] ?? 0);
        $options     = $_POST['options'] ?? [];
        $correct_idx = isset($_POST['correct_option']) ? (int)$_POST['correct_option'] : -1;

        if (empty($qtext))      $errors[] = "Question text is required.";
        if ($marks <= 0)        $errors[] = "Marks must be positive.";
        if (count($options) < 2) $errors[] = "At least 2 options required.";
        if ($correct_idx < 0)   $errors[] = "Select a correct answer.";

        $options_clean = [];
        foreach ($options as $opt) {
            $opt = sanitize($opt);
            if (!empty($opt)) $options_clean[] = $opt;
        }
        if (count($options_clean) < 2) $errors[] = "At least 2 non-empty options required.";

        if (empty($errors)) {
            $q_id = model_add_question($quiz_id, $qtext, $marks, $order_index, $ta_id);
            if ($q_id) {
                foreach ($options_clean as $i => $opt) {
                    $is_correct = ($i === $correct_idx) ? 1 : 0;
                    model_add_option($q_id, $opt, $is_correct);
                }
                set_flash('success', 'Question added.');
                redirect("/ta_project/controllers/QuizController.php?action=questions&quiz_id={$quiz_id}&course_id={$course_id}");
            } else {
                $errors[] = "Failed to add question.";
            }
        }
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/question_form.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- EDIT QUESTION ---
elseif ($action === 'edit_question') {
    $question_id = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;
    $quiz_id     = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $course_id   = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    verify_course_access($ta_id, $course_id);
    $course   = model_get_course_by_id($course_id);
    $quiz     = model_get_quiz_by_id($quiz_id);
    $question = model_get_question_by_id($question_id);
    $existing_options = model_get_question_options($question_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $qtext       = sanitize($_POST['question_text'] ?? '');
        $marks       = (float)($_POST['marks'] ?? 1);
        $order_index = (int)($_POST['order_index'] ?? 0);
        $options     = $_POST['options'] ?? [];
        $correct_idx = isset($_POST['correct_option']) ? (int)$_POST['correct_option'] : -1;

        if (empty($qtext))       $errors[] = "Question text is required.";
        if ($marks <= 0)         $errors[] = "Marks must be positive.";
        if (count($options) < 2) $errors[] = "At least 2 options required.";
        if ($correct_idx < 0)    $errors[] = "Select a correct answer.";

        $options_clean = [];
        foreach ($options as $opt) {
            $opt = sanitize($opt);
            if (!empty($opt)) $options_clean[] = $opt;
        }

        if (empty($errors)) {
            model_update_question($question_id, $qtext, $marks, $order_index);
            model_delete_options_for_question($question_id);
            foreach ($options_clean as $i => $opt) {
                $is_correct = ($i === $correct_idx) ? 1 : 0;
                model_add_option($question_id, $opt, $is_correct);
            }
            set_flash('success', 'Question updated.');
            redirect("/ta_project/controllers/QuizController.php?action=questions&quiz_id={$quiz_id}&course_id={$course_id}");
        }
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/question_form.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- DELETE QUESTION ---
elseif ($action === 'delete_question') {
    $question_id = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;
    $quiz_id     = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $course_id   = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    verify_course_access($ta_id, $course_id);
    model_delete_question($question_id);
    set_flash('success', 'Question deleted.');
    redirect("/ta_project/controllers/QuizController.php?action=questions&quiz_id={$quiz_id}&course_id={$course_id}");
}

else {
    redirect('/ta_project/controllers/CourseController.php');
}
?>
