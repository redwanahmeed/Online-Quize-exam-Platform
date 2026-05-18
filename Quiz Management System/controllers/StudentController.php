<?php
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class StudentController {
    private $studentModel;
    private $userModel;
    
    public function __construct() {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
    }
    
   
    public function index() {
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        // Get filter parameters
        $course = $_GET['course'] ?? 'all';
        $year = $_GET['year'] ?? 'all';
        $status = $_GET['status'] ?? 'all';
        
      
        $students = $this->studentModel->getFilteredStudents($course, $year, $status);
        $stats = $this->studentModel->getStats();
        
        include __DIR__ . '/../views/students/index.php';
    }
    
    public function view() {
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        $id = (int)$_GET['id'];
        $student = $this->studentModel->getStudentWithDetails($id);
        
        if (!$student) {
            header('Location: index.php?page=students&error=Student not found');
            exit();
        }
        
        include __DIR__ . '/../views/students/view.php';
    }
    
    public function create() {
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student_id = sanitize($_POST['student_id']);
            $full_name = sanitize($_POST['full_name']);
            $email = sanitize($_POST['email']);
            $phone = sanitize($_POST['phone']);
            $address = sanitize($_POST['address']);
            $course = sanitize($_POST['course']);
            $year_level = (int)$_POST['year_level'];
            
            if ($this->studentModel->getStudentByStudentId($student_id)) {
                header('Location: index.php?page=students&action=create&error=Student ID already exists');
                exit();
            }
            
            if ($this->studentModel->addStudent($student_id, $full_name, $email, $phone, $address, $course, $year_level)) {
                header('Location: index.php?page=students&success=Student added successfully');
                exit();
            } else {
                header('Location: index.php?page=students&action=create&error=Failed to add student');
                exit();
            }
        }
        include __DIR__ . '/../views/students/create.php';
    }
    
    public function edit() {
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        $id = (int)$_GET['id'];
        $student = $this->studentModel->getStudentById($id);
        
        if (!$student) {
            header('Location: index.php?page=students&error=Student not found');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = sanitize($_POST['full_name']);
            $email = sanitize($_POST['email']);
            $phone = sanitize($_POST['phone']);
            $address = sanitize($_POST['address']);
            $course = sanitize($_POST['course']);
            $year_level = (int)$_POST['year_level'];
            $status = sanitize($_POST['status']);
            
            if ($this->studentModel->updateStudent($id, $full_name, $email, $phone, $address, $course, $year_level, $status)) {
                if ($student['user_id']) {
                    $this->userModel->updateProfile($student['user_id'], $full_name, $email, $phone, $address, null);
                }
                header('Location: index.php?page=students&success=Student updated successfully');
                exit();
            } else {
                header('Location: index.php?page=students&action=edit&id=' . $id . '&error=Failed to update student');
                exit();
            }
        }
        
        include __DIR__ . '/../views/students/edit.php';
    }
    
    public function delete() {
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        $id = (int)$_GET['id'];
        $student = $this->studentModel->getStudentById($id);
        
        if (!$student) {
            header('Location: index.php?page=students&error=Student not found');
            exit();
        }
        
        if ($this->studentModel->deleteStudent($id)) {
            header('Location: index.php?page=students&success=Student deleted successfully');
            exit();
        } else {
            header('Location: index.php?page=students&error=Failed to delete student');
            exit();
        }
    }
    
    public function toggleStatus() {
        if (!hasRole('admin')) {
            echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            $student_id = (int)$_POST['student_id'];
            $current_status = $_POST['current_status'];
            $new_status = ($current_status == 'active') ? 'inactive' : 'active';
            
            $student = $this->studentModel->getStudentById($student_id);
            if (!$student) {
                echo json_encode(['status' => 'error', 'message' => 'Student not found']);
                exit();
            }
            
            if ($this->studentModel->updateStudentStatus($student_id, $new_status)) {
                if ($student['user_id']) {
                    $this->userModel->updateUserStatus($student['user_id'], $new_status);
                }
                echo json_encode(['status' => 'success', 'message' => 'Student ' . $new_status . 'd successfully']);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
                exit();
            }
        }
    }
    
    public function search() {
        if (!hasRole('admin')) {
            echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
            exit();
        }
        
        header('Content-Type: application/json');
        
        if (isset($_POST['search'])) {
            $keyword = sanitize($_POST['search']);
            
            if (strlen($keyword) < 2) {
                echo json_encode(['status' => 'error', 'message' => 'Search keyword must be at least 2 characters']);
                exit();
            }
            
            $students = $this->studentModel->searchStudents($keyword);
            echo json_encode(['status' => 'success', 'students' => $students]);
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'No search keyword provided']);
        exit();
    }
}
?>