<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id   = current_user_id();
$user    = model_get_user_by_id($ta_id);
$courses = model_get_ta_courses($ta_id);

include __DIR__ . '/../views/layouts/header.php';
include __DIR__ . '/../views/ta/dashboard.php';
include __DIR__ . '/../views/layouts/footer.php';
?>
