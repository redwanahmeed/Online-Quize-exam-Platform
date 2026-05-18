<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/UserModel.php';

header('Content-Type: application/json');


if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized - Please login']);
    exit();
}

if (!hasRole('admin')) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized - Admin access required']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    
    // Validate role - Must be one of the 4 roles
    $valid_roles = ['admin', 'instructor', 'teaching_assistant', 'student'];
    
    if (!in_array($role, $valid_roles)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid role specified']);
        exit();
    }
    
    if ($user_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
        exit();
    }
    
    $userModel = new UserModel();
    
  
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'You cannot change your own role']);
        exit();
    }
    
    if ($userModel->updateRole($user_id, $role)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Role updated successfully to ' . ucfirst(str_replace('_', ' ', $role))
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update role. Please try again.'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>