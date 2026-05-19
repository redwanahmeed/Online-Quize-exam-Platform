<?php
class QuizController extends Controller {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        // Get all quizzes with course names
        $stmt = $db->prepare("SELECT q.*, c.title as course_title FROM quizzes q 
                              LEFT JOIN courses c ON q.course_id = c.id 
                              ORDER BY q.created_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $quizzes = $result->fetch_all(MYSQLI_ASSOC);
        
        parent::view('quizzes/index', ['quizzes' => $quizzes]);
    }
    
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        // Get all courses for dropdown
        $courseStmt = $db->prepare("SELECT id, title, subject FROM courses ORDER BY created_at DESC");
        $courseStmt->execute();
        $courseResult = $courseStmt->get_result();
        $courses = $courseResult->fetch_all(MYSQLI_ASSOC);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $db->prepare("INSERT INTO quizzes (course_id, title, description, time_limit, total_marks, pass_mark, type, status, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issiiissss", 
                $_POST['course_id'],
                $_POST['title'],
                $_POST['description'],
                $_POST['time_limit'],
                $_POST['total_marks'],
                $_POST['pass_mark'],
                $_POST['type'],
                $_POST['status'],
                $_POST['start_date'],
                $_POST['end_date']
            );
            
            if ($stmt->execute()) {
                header("Location: index.php?controller=quiz&action=index&success=created");
                exit();
            } else {
                $error = "Failed to create quiz: " . $db->error;
                parent::view('quizzes/create', ['courses' => $courses, 'error' => $error]);
            }
        } else {
            parent::view('quizzes/create', ['courses' => $courses]);
        }
    }
    
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        
        // Get quiz details
        $stmt = $db->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $quiz = $result->fetch_assoc();
        
        if (!$quiz) {
            header("Location: index.php?controller=quiz&action=index");
            exit();
        }
        
        // Get all courses for dropdown
        $courseStmt = $db->prepare("SELECT id, title, subject FROM courses ORDER BY created_at DESC");
        $courseStmt->execute();
        $courseResult = $courseStmt->get_result();
        $courses = $courseResult->fetch_all(MYSQLI_ASSOC);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateStmt = $db->prepare("UPDATE quizzes SET course_id=?, title=?, description=?, time_limit=?, total_marks=?, pass_mark=?, type=?, status=?, start_date=?, end_date=? WHERE id=?");
            $updateStmt->bind_param("issiiissssi", 
                $_POST['course_id'],
                $_POST['title'],
                $_POST['description'],
                $_POST['time_limit'],
                $_POST['total_marks'],
                $_POST['pass_mark'],
                $_POST['type'],
                $_POST['status'],
                $_POST['start_date'],
                $_POST['end_date'],
                $id
            );
            
            if ($updateStmt->execute()) {
                header("Location: index.php?controller=quiz&action=index&success=updated");
                exit();
            } else {
                $error = "Failed to update quiz: " . $db->error;
                parent::view('quizzes/edit', ['quiz' => $quiz, 'courses' => $courses, 'error' => $error]);
            }
        } else {
            parent::view('quizzes/edit', ['quiz' => $quiz, 'courses' => $courses]);
        }
    }
    
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $stmt = $db->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        header("Location: index.php?controller=quiz&action=index&success=deleted");
    }
    
    public function stats() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $quizId = isset($_GET['id']) ? $_GET['id'] : 0;
        
        // Get quiz info
        $stmt = $db->prepare("SELECT q.*, c.title as course_title FROM quizzes q LEFT JOIN courses c ON q.course_id = c.id WHERE q.id = ?");
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $quiz = $result->fetch_assoc();
        
        if (!$quiz) {
            header("Location: index.php?controller=quiz&action=index");
            exit();
        }
        
        // Get statistics
        $statsStmt = $db->prepare("SELECT COUNT(*) as total_attempts, AVG(score) as average_score, MAX(score) as highest_score, MIN(score) as lowest_score, SUM(CASE WHEN passed=1 THEN 1 ELSE 0 END) as passed_count FROM quiz_attempts WHERE quiz_id = ?");
        $statsStmt->bind_param("i", $quizId);
        $statsStmt->execute();
        $statsResult = $statsStmt->get_result();
        $stats = $statsResult->fetch_assoc();
        
        if ($stats['total_attempts'] > 0) {
            $stats['pass_rate'] = ($stats['passed_count'] / $stats['total_attempts']) * 100;
        } else {
            $stats['pass_rate'] = 0;
        }
        
        // Get attempts
        $attemptStmt = $db->prepare("SELECT qa.*, u.full_name as student_name FROM quiz_attempts qa JOIN users u ON qa.student_id = u.id WHERE qa.quiz_id = ? ORDER BY qa.completed_at DESC");
        $attemptStmt->bind_param("i", $quizId);
        $attemptStmt->execute();
        $attemptResult = $attemptStmt->get_result();
        $attempts = $attemptResult->fetch_all(MYSQLI_ASSOC);
        
        parent::view('quizzes/stats', [
            'quiz' => $quiz,
            'stats' => $stats,
            'attempts' => $attempts
        ]);
    }
    
    public function getStats() {
        header('Content-Type: application/json');
        
        $database = new Database();
        $db = $database->getConnection();
        
        $quizId = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : 0;
        
        $stmt = $db->prepare("SELECT COUNT(*) as total_attempts, AVG(score) as average_score, MAX(score) as highest_score, MIN(score) as lowest_score, SUM(CASE WHEN passed=1 THEN 1 ELSE 0 END) as passed_count FROM quiz_attempts WHERE quiz_id = ?");
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        
        if ($stats['total_attempts'] > 0) {
            $stats['pass_rate'] = round(($stats['passed_count'] / $stats['total_attempts']) * 100, 2);
        } else {
            $stats['pass_rate'] = 0;
        }
        
        echo json_encode([
            'success' => true, 
            'data' => [
                'total_attempts' => (int)$stats['total_attempts'], 
                'average_score' => round($stats['average_score'], 2), 
                'highest_score' => (int)$stats['highest_score'],
                'lowest_score' => (int)$stats['lowest_score'],
                'pass_rate' => $stats['pass_rate']
            ]
        ]);
    }
}
?>