<?php
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../models/CourseModel.php';

class DashboardController {
    private $studentModel;
    private $courseModel;
    
    public function __construct() {
        $this->studentModel = new StudentModel();
        $this->courseModel = new CourseModel();
    }
    
    public function index() {
        requireAuth();
        
        $role = $_SESSION['role'];
        
        //根据不同角色显示不同内容
        switch($role) {
            case 'admin':
                // Admin can see everything
                $studentStats = $this->studentModel->getStats();
                $courses = $this->courseModel->getAllCourses();
                $students = $this->studentModel->getAllStudents();
                $recentStudents = array_slice($students, 0, 5);
                break;
                
            case 'instructor':
                // Instructor sees limited data
                $studentStats = $this->studentModel->getStats();
                $courses = $this->courseModel->getAllCourses();
                $students = $this->studentModel->getAllStudents();
                $recentStudents = array_slice($students, 0, 5);
                break;
                
            case 'teaching_assistant':
                // Teaching Assistant sees limited data
                $studentStats = $this->studentModel->getStats();
                $courses = $this->courseModel->getAllCourses();
                $students = $this->studentModel->getAllStudents();
                $recentStudents = array_slice($students, 0, 5);
                break;
                
            default: // student
                // Student sees only basic info
                $studentStats = $this->studentModel->getStats();
                $courses = $this->courseModel->getAllCourses();
                $students = [];
                $recentStudents = [];
                break;
        }
        
        include __DIR__ . '/../views/dashboard/index.php';
    }
}
?>