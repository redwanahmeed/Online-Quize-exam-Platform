<?php
require_once __DIR__ . '/../models/CourseModel.php';

class CourseController {
    private $courseModel;
    
    public function __construct() {
        $this->courseModel = new CourseModel();
    }
    
    public function index() {
        requireRole('admin');
        
        // Get filter parameters
        $subject = $_GET['subject'] ?? 'all';
        $status = $_GET['status'] ?? 'all';
        $instructor_id = $_GET['instructor'] ?? 'all';
        
        // Get filtered courses
        $courses = $this->courseModel->getFilteredCourses($subject, $status, $instructor_id);
        
        // Get filter options
        $subjects = $this->courseModel->getAllSubjects();
        $instructors = $this->courseModel->getAllInstructors();
        $stats = $this->courseModel->getCourseStats();
        
        include __DIR__ . '/../views/courses/index.php';
    }
    
    public function view() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $course = $this->courseModel->getCourseById($id);
        
        if (!$course) {
            header('Location: index.php?page=courses&error=Course not found');
            exit();
        }
        
        $enrollment_count = $this->courseModel->getEnrollmentCount($id);
        
        include __DIR__ . '/../views/courses/view.php';
    }
    
    public function create() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course_code = sanitize($_POST['course_code']);
            $course_name = sanitize($_POST['course_name']);
            $subject = sanitize($_POST['subject']);
            $description = sanitize($_POST['description']);
            $credits = (int)$_POST['credits'];
            $instructor_id = (int)$_POST['instructor_id'];
            $status = sanitize($_POST['status']);
            
            if ($this->courseModel->courseCodeExists($course_code)) {
                header('Location: index.php?page=courses&action=create&error=Course code already exists');
                exit();
            }
            
            if ($this->courseModel->addCourse($course_code, $course_name, $subject, $description, $credits, $instructor_id, $status)) {
                header('Location: index.php?page=courses&success=Course created successfully');
                exit();
            } else {
                header('Location: index.php?page=courses&action=create&error=Failed to create course');
                exit();
            }
        }
        
        $instructors = $this->courseModel->getAllInstructors();
        include __DIR__ . '/../views/courses/create.php';
    }
    
    public function edit() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $course = $this->courseModel->getCourseById($id);
        
        if (!$course) {
            header('Location: index.php?page=courses&error=Course not found');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course_code = sanitize($_POST['course_code']);
            $course_name = sanitize($_POST['course_name']);
            $subject = sanitize($_POST['subject']);
            $description = sanitize($_POST['description']);
            $credits = (int)$_POST['credits'];
            $instructor_id = (int)$_POST['instructor_id'];
            $status = sanitize($_POST['status']);
            
            if ($this->courseModel->courseCodeExists($course_code, $id)) {
                header('Location: index.php?page=courses&action=edit&id=' . $id . '&error=Course code already exists');
                exit();
            }
            
            if ($this->courseModel->updateCourse($id, $course_code, $course_name, $subject, $description, $credits, $instructor_id, $status)) {
                header('Location: index.php?page=courses&success=Course updated successfully');
                exit();
            } else {
                header('Location: index.php?page=courses&action=edit&id=' . $id . '&error=Failed to update course');
                exit();
            }
        }
        
        $instructors = $this->courseModel->getAllInstructors();
        include __DIR__ . '/../views/courses/edit.php';
    }
    
    public function delete() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $course = $this->courseModel->getCourseById($id);
        
        if (!$course) {
            header('Location: index.php?page=courses&error=Course not found');
            exit();
        }
        
        if ($this->courseModel->deleteCourse($id)) {
            header('Location: index.php?page=courses&success=Course deleted successfully');
            exit();
        } else {
            header('Location: index.php?page=courses&error=Failed to delete course');
            exit();
        }
    }
    
    public function updateStatus() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            $id = (int)$_POST['course_id'];
            $new_status = sanitize($_POST['status']);
            
            $valid_status = ['active', 'draft', 'archived'];
            if (!in_array($new_status, $valid_status)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
                exit();
            }
            
            $course = $this->courseModel->getCourseById($id);
            if (!$course) {
                echo json_encode(['status' => 'error', 'message' => 'Course not found']);
                exit();
            }
            
            if ($this->courseModel->updateCourseStatus($id, $new_status)) {
                echo json_encode(['status' => 'success', 'message' => 'Course status updated to ' . ucfirst($new_status)]);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
                exit();
            }
        }
    }
    
    public function search() {
        requireRole('admin');
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
            $keyword = sanitize($_POST['search']);
            
            if (strlen($keyword) < 2) {
                echo json_encode(['status' => 'error', 'message' => 'Search keyword must be at least 2 characters']);
                exit();
            }
            
            $courses = $this->courseModel->searchCourses($keyword);
            echo json_encode(['status' => 'success', 'courses' => $courses]);
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'No search keyword provided']);
        exit();
    }
}
?>