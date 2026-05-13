<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/StudentModel.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyword = $_POST['search'] ?? '';
    
    if (strlen($keyword) >= 2) {
        $studentModel = new StudentModel();
        $students = $studentModel->searchStudents($keyword);
        
        echo json_encode([
            'status' => 'success',
            'students' => $students
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search term too short'
        ]);
    }
}
?>