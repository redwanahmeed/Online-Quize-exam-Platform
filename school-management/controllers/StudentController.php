<?php
require_once __DIR__ . '/../models/StudentModel.php';

class StudentController {
    private $studentModel;
    
    public function __construct() {
        $this->studentModel = new StudentModel();
    }
    
    public function index() {
        // Only admin can view students
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        $students = $this->studentModel->getAllStudents();
        include __DIR__ . '/../views/students/index.php';
    }
    
    public function create() {
        // Only admin can create students
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
        // Only admin can edit students
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        $id = (int)$_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = sanitize($_POST['full_name']);
            $email = sanitize($_POST['email']);
            $phone = sanitize($_POST['phone']);
            $address = sanitize($_POST['address']);
            $course = sanitize($_POST['course']);
            $year_level = (int)$_POST['year_level'];
            $status = sanitize($_POST['status']);
            
            if ($this->studentModel->updateStudent($id, $full_name, $email, $phone, $address, $course, $year_level, $status)) {
                header('Location: index.php?page=students&success=Student updated successfully');
                exit();
            }
        }
        
        $student = $this->studentModel->getStudentById($id);
        include __DIR__ . '/../views/students/edit.php';
    }
    
    public function delete() {
        // Only admin can delete students
        if (!hasRole('admin')) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        $id = (int)$_GET['id'];
        $this->studentModel->deleteStudent($id);
        header('Location: index.php?page=students&success=Student deleted successfully');
        exit();
    }
    
    public function view() {
        // Admin, Instructor, TA can view student details
        if (!hasAnyRole(['admin', 'instructor', 'teaching_assistant'])) {
            header('Location: index.php?page=dashboard&error=Access Denied');
            exit();
        }
        
        $id = (int)$_GET['id'];
        $student = $this->studentModel->getStudentWithDetails($id);
        include __DIR__ . '/../views/students/view.php';
    }
    
    public function search() {
        // Only admin can search students
        if (!hasRole('admin')) {
            echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
            exit();
        }
        
        if (isset($_POST['search'])) {
            $keyword = sanitize($_POST['search']);
            $students = $this->studentModel->searchStudents($keyword);
            echo json_encode(['status' => 'success', 'students' => $students]);
            exit();
        }
    }
}
?>