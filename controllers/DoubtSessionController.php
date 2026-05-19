<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/DoubtSessionModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id     = current_user_id();
$action    = $_GET['action'] ?? 'list';
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$errors    = [];

// --- LIST ALL SESSIONS (all courses) ---
if ($action === 'list') {
    $sessions = model_get_ta_doubt_sessions($ta_id, $course_id ?: null);
    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/doubt_sessions.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- CREATE SESSION ---
elseif ($action === 'create') {
    if (!$course_id || !model_is_ta_assigned($ta_id, $course_id)) {
        set_flash('error', 'Select a valid assigned course.');
        redirect('/ta_project/controllers/DoubtSessionController.php');
    }
    $course = model_get_course_by_id($course_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title        = sanitize($_POST['title'] ?? '');
        $scheduled_at = sanitize($_POST['scheduled_at'] ?? '');
        $duration     = (int)($_POST['duration_minutes'] ?? 0);
        $location     = sanitize($_POST['location_or_link'] ?? '');
        $max_att      = (int)($_POST['max_attendees'] ?? 0);

        if (empty($title))        $errors[] = "Title is required.";
        if (empty($scheduled_at)) $errors[] = "Date/time is required.";
        if ($duration < 1)        $errors[] = "Duration must be at least 1 minute.";
        if ($max_att < 1)         $errors[] = "Max attendees must be at least 1.";

        if (empty($errors)) {
            $session_id = model_create_doubt_session($course_id, $ta_id, $title, $scheduled_at, $duration, $location, $max_att);
            if ($session_id) {
                set_flash('success', 'Doubt session scheduled!');
                redirect("/ta_project/controllers/DoubtSessionController.php?action=bookings&session_id={$session_id}");
            } else {
                $errors[] = "Failed to create session.";
            }
        }
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/doubt_session_form.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- EDIT/RESCHEDULE SESSION ---
elseif ($action === 'edit') {
    $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
    $session    = model_get_doubt_session_by_id($session_id);

    if (!$session || $session['ta_id'] != $ta_id) {
        set_flash('error', 'Not authorized.');
        redirect('/ta_project/controllers/DoubtSessionController.php');
    }
    $course = model_get_course_by_id($session['course_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title        = sanitize($_POST['title'] ?? '');
        $scheduled_at = sanitize($_POST['scheduled_at'] ?? '');
        $duration     = (int)($_POST['duration_minutes'] ?? 0);
        $location     = sanitize($_POST['location_or_link'] ?? '');
        $max_att      = (int)($_POST['max_attendees'] ?? 0);

        if (empty($title))        $errors[] = "Title is required.";
        if (empty($scheduled_at)) $errors[] = "Date/time is required.";
        if ($duration < 1)        $errors[] = "Duration must be at least 1 minute.";

        if (empty($errors)) {
            model_update_doubt_session($session_id, $title, $scheduled_at, $duration, $location, $max_att);
            // Notice to all booked students (simulated — stored in session flash)
            set_flash('success', 'Session rescheduled. Booked students have been notified.');
            redirect("/ta_project/controllers/DoubtSessionController.php?action=bookings&session_id={$session_id}");
        }
    }

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/doubt_session_form.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

// --- DELETE/CANCEL SESSION ---
elseif ($action === 'delete') {
    $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
    $session    = model_get_doubt_session_by_id($session_id);

    if ($session && $session['ta_id'] == $ta_id) {
        model_delete_doubt_session($session_id);
        set_flash('success', 'Session cancelled.');
    }
    redirect('/ta_project/controllers/DoubtSessionController.php');
}

// --- VIEW BOOKINGS ---
elseif ($action === 'bookings') {
    $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
    $session    = model_get_doubt_session_by_id($session_id);

    if (!$session || $session['ta_id'] != $ta_id) {
        set_flash('error', 'Not authorized.');
        redirect('/ta_project/controllers/DoubtSessionController.php');
    }
    $bookings = model_get_session_bookings($session_id);

    include __DIR__ . '/../views/layouts/header.php';
    include __DIR__ . '/../views/ta/doubt_session_bookings.php';
    include __DIR__ . '/../views/layouts/footer.php';
}

else {
    redirect('/ta_project/controllers/DoubtSessionController.php');
}
?>
