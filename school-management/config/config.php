<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');

// Site configuration
define('SITE_NAME', 'Student Management System');
define('SITE_URL', 'http://localhost/school-management/');

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create database connection
function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8");
    
    return $conn;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role - Support for 4 roles
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] == $role;
}

// Check if user has any of the allowed roles
function hasAnyRole($roles) {
    if (!isset($_SESSION['role'])) return false;
    return in_array($_SESSION['role'], $roles);
}

// Redirect if not authenticated
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'index.php?page=login');
        exit();
    }
}

// Redirect if role doesn't match
function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        header('Location: ' . SITE_URL . 'index.php?page=dashboard');
        exit();
    }
}

// Require any of the allowed roles
function requireAnyRole($roles) {
    requireAuth();
    if (!hasAnyRole($roles)) {
        header('Location: ' . SITE_URL . 'index.php?page=dashboard');
        exit();
    }
}

// Sanitize input
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Get role display name
function getRoleDisplayName($role) {
    $roles = [
        'admin' => 'Administrator',
        'instructor' => 'Instructor',
        'teaching_assistant' => 'Teaching Assistant',
        'student' => 'Student'
    ];
    return $roles[$role] ?? ucfirst($role);
}

// Check if user can manage students
function canManageStudents() {
    return hasRole('admin');
}

// Check if user can view students
function canViewStudents() {
    return hasAnyRole(['admin', 'instructor', 'teaching_assistant']);
}

// Check if user can manage courses
function canManageCourses() {
    return hasRole('admin');
}

// Check if user can manage users
function canManageUsers() {
    return hasRole('admin');
}

// Redirect based on role after login
function redirectBasedOnRole() {
    $role = $_SESSION['role'];
    
    switch($role) {
        case 'admin':
            header('Location: index.php?page=dashboard');
            break;
        case 'instructor':
            header('Location: index.php?page=dashboard');
            break;
        case 'teaching_assistant':
            header('Location: index.php?page=dashboard');
            break;
        case 'student':
            header('Location: index.php?page=dashboard');
            break;
        default:
            header('Location: index.php?page=login');
            break;
    }
    exit();
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Get user IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Log user activity
function logActivity($user_id, $action) {
    $conn = getDB();
    $ip = getUserIP();
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $action, $ip);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// Check if email exists
function emailExists($email) {
    $conn = getDB();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $exists;
}

// Check if username exists
function usernameExists($username) {
    $conn = getDB();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $exists;
}

// Get total counts for dashboard
function getTotalCounts() {
    $conn = getDB();
    $counts = [];
    
    // Total students
    $result = $conn->query("SELECT COUNT(*) as total FROM students");
    $counts['students'] = $result->fetch_assoc()['total'];
    
    // Total courses
    $result = $conn->query("SELECT COUNT(*) as total FROM courses");
    $counts['courses'] = $result->fetch_assoc()['total'];
    
    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    $counts['users'] = $result->fetch_assoc()['total'];
    
    // Total enrollments
    $result = $conn->query("SELECT COUNT(*) as total FROM enrollments");
    $counts['enrollments'] = $result->fetch_assoc()['total'];
    
    $conn->close();
    return $counts;
}

// Format date
function formatDate($date, $format = 'F d, Y') {
    return date($format, strtotime($date));
}

// Redirect with message
function redirectWithMessage($url, $message, $type = 'success') {
    header("Location: $url&{$type}=" . urlencode($message));
    exit();
}

// Upload file function
function uploadFile($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    $filename = $file['name'];
    $tmp_name = $file['tmp_name'];
    $error = $file['error'];
    $size = $file['size'];
    
    // Check for errors
    if ($error !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    // Check file size (max 2MB)
    if ($size > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large (max 2MB)'];
    }
    
    // Get file extension
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // Check file type
    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '_' . time() . '.' . $ext;
    $target_file = $target_dir . $new_filename;
    
    // Create directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($tmp_name, $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
}
?>