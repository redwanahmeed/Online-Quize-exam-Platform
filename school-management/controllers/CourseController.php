<?php
require_once __DIR__ . '/../models/CourseModel.php';

class CourseController {
    private $courseModel;
    
    public function __construct() {
        $this->courseModel = new CourseModel();
    }
    
    public function index() {
        requireRole('admin');
        include __DIR__ . '/../views/courses/index.php';
    }
    
    public function create() {
        requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course_code = sanitize($_POST['course_code']);
            $course_name = sanitize($_POST['course_name']);
            $description = sanitize($_POST['description']);
            $credits = (int)$_POST['credits'];
            
            if ($this->courseModel->addCourse($course_code, $course_name, $description, $credits)) {
                header('Location: index.php?page=courses&success=1');
                exit();
            }
        }
        include __DIR__ . '/../views/courses/create.php';
    }
    
    public function edit() {
        requireRole('admin');
        $id = (int)$_GET['id'];
        $course = $this->courseModel->getCourseById($id);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course_code = sanitize($_POST['course_code']);
            $course_name = sanitize($_POST['course_name']);
            $description = sanitize($_POST['description']);
            $credits = (int)$_POST['credits'];
            $status = sanitize($_POST['status']);
            
            if ($this->courseModel->updateCourse($id, $course_code, $course_name, $description, $credits, $status)) {
                header('Location: index.php?page=courses&updated=1');
                exit();
            }
        }
        
        include __DIR__ . '/../views/courses/edit.php';
    }
    
    public function delete() {
        requireRole('admin');
        $id = (int)$_GET['id'];
        $this->courseModel->deleteCourse($id);
        header('Location: index.php?page=courses&deleted=1');
        exit();
    }
}
?>