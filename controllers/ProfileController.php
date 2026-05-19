<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_role('ta');

$ta_id = current_user_id();
$user  = model_get_user_by_id($ta_id);
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name    = sanitize($_POST['name'] ?? '');
        $phone   = sanitize($_POST['phone'] ?? '');
        $program = sanitize($_POST['program'] ?? '');

        if (empty($name))  $errors[] = "Name is required.";
        if (strlen($name) > 100) $errors[] = "Name too long (max 100 chars).";

        if (empty($errors)) {
            if (model_update_profile($ta_id, $name, $phone, $program)) {
                $_SESSION['user_name'] = $name;
                $success = "Profile updated successfully.";
                $user = model_get_user_by_id($ta_id);
            } else {
                $errors[] = "Failed to update profile.";
            }
        }
    }

    if ($action === 'change_password') {
        $current  = $_POST['current_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($current)) $errors[] = "Current password is required.";
        if (empty($new_pass)) $errors[] = "New password is required.";
        if (strlen($new_pass) < 6) $errors[] = "Password must be at least 6 characters.";
        if ($new_pass !== $confirm) $errors[] = "Passwords do not match.";

        if (empty($errors)) {
            if (!password_verify($current, $user['password_hash'])) {
                $errors[] = "Current password is incorrect.";
            } else {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                if (model_update_password($ta_id, $hash)) {
                    $success = "Password changed successfully.";
                } else {
                    $errors[] = "Failed to change password.";
                }
            }
        }
    }
}

include __DIR__ . '/../views/layouts/header.php';
include __DIR__ . '/../views/ta/profile.php';
include __DIR__ . '/../views/layouts/footer.php';
?>
