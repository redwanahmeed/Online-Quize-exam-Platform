<?php
class Course extends Model {
    
    public function getByInstructor($instructorId) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE instructor_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $instructorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getPublishedCourses($instructorId) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE instructor_id = ? AND status = 'published' ORDER BY created_at DESC");
        $stmt->bind_param("i", $instructorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getDraftCourses($instructorId) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE instructor_id = ? AND status = 'draft' ORDER BY created_at DESC");
        $stmt->bind_param("i", $instructorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getArchivedCourses($instructorId) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE instructor_id = ? AND status = 'archived' ORDER BY created_at DESC");
        $stmt->bind_param("i", $instructorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function create($instructor_id, $title, $subject, $description, $enrollment_type, $max_students, $status) {
        $stmt = $this->db->prepare("INSERT INTO courses (instructor_id, title, subject, description, enrollment_type, max_students, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssis", $instructor_id, $title, $subject, $description, $enrollment_type, $max_students, $status);
        return $stmt->execute();
    }
    
    public function update($id, $title, $subject, $description, $enrollment_type, $max_students, $status) {
        $stmt = $this->db->prepare("UPDATE courses SET title=?, subject=?, description=?, enrollment_type=?, max_students=?, status=? WHERE id=?");
        $stmt->bind_param("ssssisi", $title, $subject, $description, $enrollment_type, $max_students, $status, $id);
        return $stmt->execute();
    }
    
    public function archive($id) {
        $stmt = $this->db->prepare("UPDATE courses SET status='archived' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function publish($id) {
        $stmt = $this->db->prepare("UPDATE courses SET status='published' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function unpublish($id) {
        $stmt = $this->db->prepare("UPDATE courses SET status='draft' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getEnrolledCount($courseId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id=? AND status='approved'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data ? $data['count'] : 0;
    }
    
    public function getPendingCount($courseId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id=? AND status='pending'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data ? $data['count'] : 0;
    }
    
    public function getCourseWithDetails($courseId) {
        $stmt = $this->db->prepare("SELECT c.*, u.full_name as instructor_name FROM courses c JOIN users u ON c.instructor_id = u.id WHERE c.id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>