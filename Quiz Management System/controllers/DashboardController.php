<?php
require_once __DIR__ . '/../models/DashboardModel.php';
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../models/CourseModel.php';

class DashboardController {
    private $dashboardModel;
    private $studentModel;
    private $courseModel;
    
    public function __construct() {
        $this->dashboardModel = new DashboardModel();
        $this->studentModel = new StudentModel();
        $this->courseModel = new CourseModel();
    }
    
    public function index() {
        requireAuth();
        
        $role = $_SESSION['role'];
        
        if ($role == 'admin') {
           
            $userCounts = $this->dashboardModel->getUserCounts();
            $activeCourses = $this->dashboardModel->getActiveCoursesCount();
            $todayQuizAttempts = $this->dashboardModel->getTodayQuizAttempts();
            $pendingRequests = $this->dashboardModel->getPendingInstructorRequests();
            $recentQuizAttempts = $this->dashboardModel->getRecentQuizAttempts(5);
            $pendingRequestsDetails = $this->dashboardModel->getPendingRequestsDetails();
            $totalCourses = $this->dashboardModel->getTotalCourses();
            $totalEnrollments = $this->dashboardModel->getTotalEnrollments();
            $weeklyStats = $this->dashboardModel->getWeeklyStats();
            
            include __DIR__ . '/../views/dashboard/admin.php';
        } else {
            
            $userCounts = $this->dashboardModel->getUserCounts();
            $activeCourses = $this->dashboardModel->getActiveCoursesCount();
            include __DIR__ . '/../views/dashboard/index.php';
        }
    }
    
    public function approveRequest() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $request_id = (int)$_POST['request_id'];
            $reviewed_by = $_SESSION['user_id'];
            
            if ($this->dashboardModel->approveInstructorRequest($request_id, $reviewed_by)) {
                header('Location: index.php?page=dashboard&success=Instructor request approved');
                exit();
            } else {
                header('Location: index.php?page=dashboard&error=Failed to approve request');
                exit();
            }
        }
    }
    
    public function rejectRequest() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $request_id = (int)$_POST['request_id'];
            $reviewed_by = $_SESSION['user_id'];
            
            if ($this->dashboardModel->rejectInstructorRequest($request_id, $reviewed_by)) {
                header('Location: index.php?page=dashboard&success=Instructor request rejected');
                exit();
            } else {
                header('Location: index.php?page=dashboard&error=Failed to reject request');
                exit();
            }
        }
    }
}
?>