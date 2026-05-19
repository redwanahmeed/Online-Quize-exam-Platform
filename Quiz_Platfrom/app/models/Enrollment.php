<?php
class Enrollment extends Model {
    
    public function getPendingRequests($courseId) {
        $stmt = $this->db->prepare("SELECT e.*, u.full_name, u.email FROM enrollments e JOIN users u ON e.student_id = u.id WHERE e.course_id = ? AND e.status = 'pending'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function updateStatus($enrollmentId, $status) {
        $stmt = $this->db->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $enrollmentId);
        return $stmt->execute();
    }
    
    public function getEnrolledStudents($courseId) {
        $stmt = $this->db->prepare("SELECT u.*, e.enrolled_at FROM enrollments e JOIN users u ON e.student_id = u.id WHERE e.course_id = ? AND e.status = 'approved' ORDER BY e.enrolled_at DESC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllEnrollmentsByCourse($courseId) {
        $stmt = $this->db->prepare("SELECT e.*, u.full_name, u.email, u.username FROM enrollments e JOIN users u ON e.student_id = u.id WHERE e.course_id = ? ORDER BY e.enrolled_at DESC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>