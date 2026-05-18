<?php
session_start();


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');


define('SITE_NAME', 'Student Management System');
define('SITE_URL', 'http://localhost/school-management/');


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create database connection
function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
  
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
  
    $conn->set_charset("utf8");
    
    return $conn;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] == $role;
}


function hasAnyRole($roles) {
    if (!isset($_SESSION['role'])) return false;
    return in_array($_SESSION['role'], $roles);
}


function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'index.php?page=login');
        exit();
    }
}

function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        header('Location: ' . SITE_URL . 'index.php?page=dashboard');
        exit();
    }
}


function requireAnyRole($roles) {
    requireAuth();
    if (!hasAnyRole($roles)) {
        header('Location: ' . SITE_URL . 'index.php?page=dashboard');
        exit();
    }
}


function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}


function getRoleDisplayName($role) {
    $roles = [
        'admin' => 'Administrator',
        'instructor' => 'Instructor',
        'teaching_assistant' => 'Teaching Assistant',
        'student' => 'Student'
    ];
    return $roles[$role] ?? ucfirst($role);
}


function canManageStudents() {
    return hasRole('admin');
}


function canViewStudents() {
    return hasAnyRole(['admin', 'instructor', 'teaching_assistant']);
}


function canManageCourses() {
    return hasRole('admin');
}


function canManageUsers() {
    return hasRole('admin');
}


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


function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}


function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}


function logActivity($user_id, $action) {
    $conn = getDB();
    $ip = getUserIP();
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $action, $ip);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}


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


function getTotalCounts() {
    $conn = getDB();
    $counts = [];
    
   
    $result = $conn->query("SELECT COUNT(*) as total FROM students");
    $counts['students'] = $result->fetch_assoc()['total'];
    
   
    $result = $conn->query("SELECT COUNT(*) as total FROM courses");
    $counts['courses'] = $result->fetch_assoc()['total'];
    
  
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    $counts['users'] = $result->fetch_assoc()['total'];
    
  
    $result = $conn->query("SELECT COUNT(*) as total FROM enrollments");
    $counts['enrollments'] = $result->fetch_assoc()['total'];
    
    $conn->close();
    return $counts;
}


function formatDate($date, $format = 'F d, Y') {
    return date($format, strtotime($date));
}


function redirectWithMessage($url, $message, $type = 'success') {
    header("Location: $url&{$type}=" . urlencode($message));
    exit();
}


function uploadFile($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    $filename = $file['name'];
    $tmp_name = $file['tmp_name'];
    $error = $file['error'];
    $size = $file['size'];
    

    if ($error !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }
    

    if ($size > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large (max 2MB)'];
    }
    
   
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    

    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
  
    $new_filename = uniqid() . '_' . time() . '.' . $ext;
    $target_file = $target_dir . $new_filename;
    
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($tmp_name, $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
}
?>