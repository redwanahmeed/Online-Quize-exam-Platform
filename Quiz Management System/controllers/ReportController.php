<?php
require_once __DIR__ . '/../models/ReportModel.php';

class ReportController {
    private $reportModel;
    
    public function __construct() {
        $this->reportModel = new ReportModel();
    }
    
    public function index() {
        requireRole('admin');
        
        $search_result = null;
        $student = null;
        $courses = null;
        $quiz_attempts = null;
        $quiz_stats = null;
        $performance = null;
        $quiz_details = null;
        
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $keyword = sanitize($_GET['search']);
            $search_result = $this->reportModel->searchStudents($keyword);
        }
        
        if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
            $student_id = (int)$_GET['student_id'];
            $student = $this->reportModel->getStudentById($student_id);
            
            if ($student) {
                $courses = $this->reportModel->getStudentCourses($student_id);
                $quiz_attempts = $this->reportModel->getStudentQuizAttempts($student_id);
                $quiz_stats = $this->reportModel->getQuizStatistics($student_id);
                $performance = $this->reportModel->getPerformanceSummary($student_id);
                $quiz_details = $quiz_stats['quiz_details'] ?? [];
            }
        }
        
        include __DIR__ . '/../views/reports/academic.php';
    }
    
    public function search() {
        requireRole('admin');
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
            $keyword = sanitize($_POST['search']);
            
            if (strlen($keyword) < 2) {
                echo json_encode(['status' => 'error', 'message' => 'Enter at least 2 characters']);
                exit();
            }
            
            $students = $this->reportModel->searchStudents($keyword);
            echo json_encode(['status' => 'success', 'students' => $students]);
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'No search keyword']);
        exit();
    }
}
?>