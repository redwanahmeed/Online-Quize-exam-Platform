<?php
require_once "../core/Model.php";

class Quiz extends Model {
    protected $table = "quizzes";
    
    public function getByInstructor($instructorId) {
        $sql = "SELECT q.*, c.title as course_title 
                FROM quizzes q 
                JOIN courses c ON q.course_id = c.id 
                WHERE c.instructor_id = ? 
                ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $instructorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO quizzes (course_id, title, description, time_limit, total_marks, pass_mark, type, status, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiiissss", $data['course_id'], $data['title'], $data['description'], $data['time_limit'], $data['total_marks'], $data['pass_mark'], $data['type'], $data['status'], $data['start_date'], $data['end_date']);
        return $stmt->execute();
    }
    
    public function getStatistics($quizId) {
        $sql = "SELECT 
                    COUNT(*) as total_attempts,
                    AVG(score) as average_score,
                    MAX(score) as highest_score,
                    MIN(score) as lowest_score,
                    SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_count
                FROM quiz_attempts 
                WHERE quiz_id = ? AND completed_at IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        
        if ($stats['total_attempts'] > 0) {
            $stats['pass_rate'] = ($stats['passed_count'] / $stats['total_attempts']) * 100;
        } else {
            $stats['pass_rate'] = 0;
        }
        
        return $stats;
    }
    
    public function getAttempts($quizId) {
        $sql = "SELECT qa.*, u.full_name as student_name 
                FROM quiz_attempts qa 
                JOIN users u ON qa.student_id = u.id 
                WHERE qa.quiz_id = ? 
                ORDER BY qa.completed_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>