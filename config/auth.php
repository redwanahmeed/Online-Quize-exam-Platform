<?php
function require_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: /ta_project/index.php");
        exit();
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        header("Location: /ta_project/index.php?error=unauthorized");
        exit();
    }
}

function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_role() {
    return $_SESSION['role'] ?? null;
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function set_flash($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $msg = ['type' => $_SESSION['flash_type'], 'message' => $_SESSION['flash_message']];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $msg;
    }
    return null;
}
?>
