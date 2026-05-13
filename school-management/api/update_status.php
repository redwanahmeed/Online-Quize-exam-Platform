<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/StudentModel.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !hasRole('admin')) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = (int)$_POST['student_id'];
    $status = $_POST['status'];
    
    $studentModel = new StudentModel();
    if ($studentModel->updateStatus($student_id, $status)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Status updated successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update status'
        ]);
    }
}
?>