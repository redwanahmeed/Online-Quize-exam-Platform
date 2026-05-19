<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lms_instructor');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>