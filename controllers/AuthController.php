<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function ctrl_login() {
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Server-side validation
        if (empty($email))    $errors[] = "Email is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
        if (empty($password)) $errors[] = "Password is required.";

        if (empty($errors)) {
            $user = model_get_user_by_email($email);
            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['role'] !== 'ta') {
                    $errors[] = "Access denied. This portal is for Teaching Assistants only.";
                } else {
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['role']      = $user['role'];
                    $_SESSION['email']     = $user['email'];

                    // Remember Me
                    if (isset($_POST['remember_me'])) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (30 * 24 * 3600), '/');
                        setcookie('remember_user', $user['id'], time() + (30 * 24 * 3600), '/');
                    }

                    redirect('/ta_project/controllers/DashboardController.php');
                }
            } else {
                $errors[] = "Invalid email or password.";
            }
        }
    }

    // Check remember me cookie
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
        $uid = (int)$_COOKIE['remember_user'];
        $user = model_get_user_by_id($uid);
        if ($user && $user['role'] === 'ta') {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['email']     = $user['email'];
            redirect('/ta_project/controllers/DashboardController.php');
        }
    }

    include __DIR__ . '/../views/auth/login.php';
}

function ctrl_logout() {
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    setcookie('remember_user', '', time() - 3600, '/');
    redirect('/ta_project/index.php');
}

// Route
$action = $_GET['action'] ?? 'login';
if ($action === 'logout') {
    ctrl_logout();
} else {
    ctrl_login();
}
?>
