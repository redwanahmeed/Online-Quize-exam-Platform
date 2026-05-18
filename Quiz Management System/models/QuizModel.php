<?php
require_once __DIR__ . '/../config/config.php';

class QuizModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Get all quizzes with course and attempt info
    public function getAllQuizzes() {
        $sql = "SELECT q.*, c.course_name, c.course_code,
                (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as total_attempts,
                (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id AND status = 'completed') as completed_attempts,
                (SELECT AVG(percentage) FROM quiz_attempts WHERE quiz_id = q.id AND status = 'completed') as avg_percentage
                FROM quizzes q 
                LEFT JOIN courses c ON q.course_id = c.id 
                ORDER BY q.created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get quiz by ID
    public function getQuizById($id) {
        $stmt = $this->conn->prepare("SELECT q.*, c.course_name, c.course_code 
                                      FROM quizzes q 
                                      LEFT JOIN courses c ON q.course_id = c.id 
                                      WHERE q.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get filtered quizzes
    public function getFilteredQuizzes($course_id = null, $status = null, $quiz_type = null) {
        $sql = "SELECT q.*, c.course_name, c.course_code,
                (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as total_attempts,
                (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id AND status = 'completed') as completed_attempts,
                (SELECT AVG(percentage) FROM quiz_attempts WHERE quiz_id = q.id AND status = 'completed') as avg_percentage
                FROM quizzes q 
                LEFT JOIN courses c ON q.course_id = c.id 
                WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($course_id && $course_id != 'all') {
            $sql .= " AND q.course_id = ?";
            $params[] = $course_id;
            $types .= "i";
        }
        
        if ($status && $status != 'all') {
            $sql .= " AND q.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        if ($quiz_type && $quiz_type != 'all') {
            $sql .= " AND q.quiz_type = ?";
            $params[] = $quiz_type;
            $types .= "s";
        }
        
        $sql .= " ORDER BY q.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get quiz attempts
    public function getQuizAttempts($quiz_id) {
        $stmt = $this->conn->prepare("SELECT qa.*, s.full_name as student_name, s.student_id 
                                      FROM quiz_attempts qa 
                                      JOIN students s ON qa.student_id = s.id 
                                      WHERE qa.quiz_id = ? 
                                      ORDER BY qa.attempt_date DESC");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get all courses for filter
    public function getAllCourses() {
        $result = $this->conn->query("SELECT id, course_name, course_code FROM courses WHERE status = 'active' ORDER BY course_name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get quiz statistics
    public function getQuizStats() {
        $stats = [];
        
        $result = $this->conn->query("SELECT COUNT(*) as total FROM quizzes");
        $stats['total'] = $result->fetch_assoc()['total'];
        
        $result = $this->conn->query("SELECT COUNT(*) as active FROM quizzes WHERE status = 'active'");
        $stats['active'] = $result->fetch_assoc()['active'];
        
        $result = $this->conn->query("SELECT COUNT(*) as draft FROM quizzes WHERE status = 'draft'");
        $stats['draft'] = $result->fetch_assoc()['draft'];
        
        $result = $this->conn->query("SELECT COUNT(*) as archived FROM quizzes WHERE status = 'archived'");
        $stats['archived'] = $result->fetch_assoc()['archived'];
        
        $result = $this->conn->query("SELECT COUNT(*) as total_attempts FROM quiz_attempts");
        $stats['total_attempts'] = $result->fetch_assoc()['total_attempts'];
        
        return $stats;
    }
    
    // Get quiz types
    public function getQuizTypes() {
        return ['exam', 'assignment', 'test', 'practice'];
    }
    
    // Add quiz
    public function addQuiz($course_id, $quiz_title, $quiz_type, $description, $total_marks, $passing_marks, $duration_minutes, $status) {
        $stmt = $this->conn->prepare("INSERT INTO quizzes (course_id, quiz_title, quiz_type, description, total_marks, passing_marks, duration_minutes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssddds", $course_id, $quiz_title, $quiz_type, $description, $total_marks, $passing_marks, $duration_minutes, $status);
        return $stmt->execute();
    }
    
    // Update quiz
    public function updateQuiz($id, $course_id, $quiz_title, $quiz_type, $description, $total_marks, $passing_marks, $duration_minutes, $status) {
        $stmt = $this->conn->prepare("UPDATE quizzes SET course_id = ?, quiz_title = ?, quiz_type = ?, description = ?, total_marks = ?, passing_marks = ?, duration_minutes = ?, status = ? WHERE id = ?");
        $stmt->bind_param("isssdddsi", $course_id, $quiz_title, $quiz_type, $description, $total_marks, $passing_marks, $duration_minutes, $status, $id);
        return $stmt->execute();
    }
    
    // Update quiz status
    public function updateQuizStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE quizzes SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    
    // Delete quiz
    public function deleteQuiz($id) {
        $stmt = $this->conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Search quizzes
    public function searchQuizzes($keyword) {
        $keyword = "%{$keyword}%";
        $sql = "SELECT q.*, c.course_name, c.course_code,
                (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as total_attempts
                FROM quizzes q 
                LEFT JOIN courses c ON q.course_id = c.id 
                WHERE q.quiz_title LIKE ? OR c.course_name LIKE ? 
                ORDER BY q.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>