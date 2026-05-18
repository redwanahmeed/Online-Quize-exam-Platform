<?php
require_once __DIR__ . '/../models/QuizModel.php';

class QuizController {
    private $quizModel;
    
    public function __construct() {
        $this->quizModel = new QuizModel();
    }
    
    public function index() {
        requireRole('admin');
        
        // Get filter parameters
        $course_id = $_GET['course'] ?? 'all';
        $status = $_GET['status'] ?? 'all';
        $quiz_type = $_GET['type'] ?? 'all';
        
        // Get filtered quizzes
        $quizzes = $this->quizModel->getFilteredQuizzes($course_id, $status, $quiz_type);
        
        // Get filter options
        $courses = $this->quizModel->getAllCourses();
        $quizTypes = $this->quizModel->getQuizTypes();
        $stats = $this->quizModel->getQuizStats();
        
        include __DIR__ . '/../views/quizzes/index.php';
    }
    
    public function view() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $quiz = $this->quizModel->getQuizById($id);
        
        if (!$quiz) {
            header('Location: index.php?page=quizzes&error=Quiz not found');
            exit();
        }
        
        $attempts = $this->quizModel->getQuizAttempts($id);
        
        include __DIR__ . '/../views/quizzes/view.php';
    }
    
    public function create() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course_id = (int)$_POST['course_id'];
            $quiz_title = sanitize($_POST['quiz_title']);
            $quiz_type = sanitize($_POST['quiz_type']);
            $description = sanitize($_POST['description']);
            $total_marks = (float)$_POST['total_marks'];
            $passing_marks = (float)$_POST['passing_marks'];
            $duration_minutes = (int)$_POST['duration_minutes'];
            $status = sanitize($_POST['status']);
            
            if ($this->quizModel->addQuiz($course_id, $quiz_title, $quiz_type, $description, $total_marks, $passing_marks, $duration_minutes, $status)) {
                header('Location: index.php?page=quizzes&success=Quiz created successfully');
                exit();
            } else {
                header('Location: index.php?page=quizzes&action=create&error=Failed to create quiz');
                exit();
            }
        }
        
        $courses = $this->quizModel->getAllCourses();
        $quizTypes = $this->quizModel->getQuizTypes();
        include __DIR__ . '/../views/quizzes/create.php';
    }
    
    public function edit() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $quiz = $this->quizModel->getQuizById($id);
        
        if (!$quiz) {
            header('Location: index.php?page=quizzes&error=Quiz not found');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course_id = (int)$_POST['course_id'];
            $quiz_title = sanitize($_POST['quiz_title']);
            $quiz_type = sanitize($_POST['quiz_type']);
            $description = sanitize($_POST['description']);
            $total_marks = (float)$_POST['total_marks'];
            $passing_marks = (float)$_POST['passing_marks'];
            $duration_minutes = (int)$_POST['duration_minutes'];
            $status = sanitize($_POST['status']);
            
            if ($this->quizModel->updateQuiz($id, $course_id, $quiz_title, $quiz_type, $description, $total_marks, $passing_marks, $duration_minutes, $status)) {
                header('Location: index.php?page=quizzes&success=Quiz updated successfully');
                exit();
            } else {
                header('Location: index.php?page=quizzes&action=edit&id=' . $id . '&error=Failed to update quiz');
                exit();
            }
        }
        
        $courses = $this->quizModel->getAllCourses();
        $quizTypes = $this->quizModel->getQuizTypes();
        include __DIR__ . '/../views/quizzes/edit.php';
    }
    
    public function delete() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        
        if ($this->quizModel->deleteQuiz($id)) {
            header('Location: index.php?page=quizzes&success=Quiz deleted successfully');
            exit();
        } else {
            header('Location: index.php?page=quizzes&error=Failed to delete quiz');
            exit();
        }
    }
    
    public function updateStatus() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            $id = (int)$_POST['quiz_id'];
            $new_status = sanitize($_POST['status']);
            
            $valid_status = ['active', 'draft', 'archived'];
            if (!in_array($new_status, $valid_status)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
                exit();
            }
            
            if ($this->quizModel->updateQuizStatus($id, $new_status)) {
                echo json_encode(['status' => 'success', 'message' => 'Quiz status updated to ' . ucfirst($new_status)]);
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
            
            $quizzes = $this->quizModel->searchQuizzes($keyword);
            echo json_encode(['status' => 'success', 'quizzes' => $quizzes]);
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'No search keyword provided']);
        exit();
    }
}
?>