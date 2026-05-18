<?php
require_once __DIR__ . '/../config/config.php';

class DashboardModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Get user counts by role
    public function getUserCounts() {
        $counts = [];
        
        // Total users
        $result = $this->conn->query("SELECT COUNT(*) as total FROM users");
        $counts['total_users'] = $result->fetch_assoc()['total'];
        
        // Admin count
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $counts['admins'] = $result->fetch_assoc()['count'];
        
        // Instructor count
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'instructor'");
        $counts['instructors'] = $result->fetch_assoc()['count'];
        
        // Teaching Assistant count
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'teaching_assistant'");
        $counts['teaching_assistants'] = $result->fetch_assoc()['count'];
        
        // Student count
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
        $counts['students'] = $result->fetch_assoc()['count'];
        
        return $counts;
    }
    
    // Get active courses count
    public function getActiveCoursesCount() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM courses WHERE status = 'active'");
        $data = $result->fetch_assoc();
        return $data['count'];
    }
    
    // Get today's quiz attempts count
    public function getTodayQuizAttempts() {
        // Check if quiz_attempts table exists
        $result = $this->conn->query("SHOW TABLES LIKE 'quiz_attempts'");
        if ($result->num_rows == 0) {
            return 0;
        }
        
        $result = $this->conn->query("SELECT COUNT(*) as count FROM quiz_attempts WHERE attempt_date = CURDATE()");
        if ($result) {
            $data = $result->fetch_assoc();
            return $data['count'];
        }
        return 0;
    }
    
    // Get pending instructor approval requests
    public function getPendingInstructorRequests() {
        // Check if instructor_requests table exists
        $result = $this->conn->query("SHOW TABLES LIKE 'instructor_requests'");
        if ($result->num_rows == 0) {
            return 0;
        }
        
        $result = $this->conn->query("SELECT COUNT(*) as count FROM instructor_requests WHERE status = 'pending'");
        if ($result) {
            $data = $result->fetch_assoc();
            return $data['count'];
        }
        return 0;
    }
    
    // Get all pending instructor requests with details
    public function getPendingRequestsDetails() {
        // Check if instructor_requests table exists
        $result = $this->conn->query("SHOW TABLES LIKE 'instructor_requests'");
        if ($result->num_rows == 0) {
            return [];
        }
        
        $result = $this->conn->query("SELECT * FROM instructor_requests WHERE status = 'pending' ORDER BY request_date DESC");
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // Get recent quiz attempts (Fixed version)
// Get recent quiz attempts with proper joins
public function getRecentQuizAttempts($limit = 5) {
    // Check if tables exist
    $tables = ['quiz_attempts', 'students', 'quizzes'];
    foreach ($tables as $table) {
        $result = $this->conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            return [];
        }
    }
    
    // Proper query with JOIN
    $sql = "SELECT qa.*, 
            s.full_name as student_name, 
            s.student_id,
            q.quiz_title, 
            q.total_marks
            FROM quiz_attempts qa 
            LEFT JOIN students s ON qa.student_id = s.id 
            LEFT JOIN quizzes q ON qa.quiz_id = q.id 
            ORDER BY qa.attempt_date DESC 
            LIMIT ?";
    
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        // Fallback: simple query without joins
        $result = $this->conn->query("SELECT * FROM quiz_attempts ORDER BY attempt_date DESC LIMIT $limit");
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
    
    // Get total courses
    public function getTotalCourses() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM courses");
        $data = $result->fetch_assoc();
        return $data['count'];
    }
    
    // Get total enrollments
    public function getTotalEnrollments() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM enrollments");
        $data = $result->fetch_assoc();
        return $data['count'];
    }
    
    // Get weekly statistics
    public function getWeeklyStats() {
        $stats = [];
        
        // Last 7 days quiz attempts
        $result = $this->conn->query("SELECT DATE(attempt_date) as date, COUNT(*) as count 
                                      FROM quiz_attempts 
                                      WHERE attempt_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                      GROUP BY DATE(attempt_date)");
        if ($result) {
            $stats['weekly_quiz_attempts'] = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $stats['weekly_quiz_attempts'] = [];
        }
        
        // Last 7 days new enrollments
        $result = $this->conn->query("SELECT DATE(enrollment_date) as date, COUNT(*) as count 
                                      FROM enrollments 
                                      WHERE enrollment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                      GROUP BY DATE(enrollment_date)");
        if ($result) {
            $stats['weekly_enrollments'] = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $stats['weekly_enrollments'] = [];
        }
        
        return $stats;
    }
    
    // Approve instructor request
    public function approveInstructorRequest($request_id, $reviewed_by) {
        $stmt = $this->conn->prepare("UPDATE instructor_requests SET status = 'approved', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $reviewed_by, $request_id);
        
        if ($stmt->execute()) {
            // Get the user_id from the request
            $stmt2 = $this->conn->prepare("SELECT user_id FROM instructor_requests WHERE id = ?");
            $stmt2->bind_param("i", $request_id);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $request = $result->fetch_assoc();
            
            // Update user role to instructor
            if ($request) {
                $stmt3 = $this->conn->prepare("UPDATE users SET role = 'instructor' WHERE id = ?");
                $stmt3->bind_param("i", $request['user_id']);
                $stmt3->execute();
            }
            return true;
        }
        return false;
    }
    
    // Reject instructor request
    public function rejectInstructorRequest($request_id, $reviewed_by) {
        $stmt = $this->conn->prepare("UPDATE instructor_requests SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $reviewed_by, $request_id);
        return $stmt->execute();
    }
}
?>