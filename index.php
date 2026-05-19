<?php
require_once __DIR__ . '/config/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// If already logged in and is TA, redirect to dashboard
if (is_logged_in() && current_role() === 'ta') {
    header("Location: /ta_project/controllers/DashboardController.php");
    exit();
}

// Show login
require_once __DIR__ . '/controllers/AuthController.php';
?>
