<?php
class Ta extends Model {
    
    public function getAllTAs() {
        $result = $this->db->query("SELECT * FROM users WHERE role = 'ta' ORDER BY full_name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getTAsByCourse($courseId) {
        $stmt = $this->db->prepare("SELECT u.*, ct.assigned_at 
                                    FROM course_tas ct 
                                    JOIN users u ON ct.ta_id = u.id 
                                    WHERE ct.course_id = ? 
                                    ORDER BY ct.assigned_at DESC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function isTaAssigned($courseId, $taId) {
        $stmt = $this->db->prepare("SELECT id FROM course_tas WHERE course_id = ? AND ta_id = ?");
        $stmt->bind_param("ii", $courseId, $taId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    public function assignTaToCourse($courseId, $taId) {
        if ($this->isTaAssigned($courseId, $taId)) {
            return false;
        }
        $stmt = $this->db->prepare("INSERT INTO course_tas (course_id, ta_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $courseId, $taId);
        return $stmt->execute();
    }
    
    public function removeTaFromCourse($courseId, $taId) {
        $stmt = $this->db->prepare("DELETE FROM course_tas WHERE course_id = ? AND ta_id = ?");
        $stmt->bind_param("ii", $courseId, $taId);
        return $stmt->execute();
    }
    
    public function getCoursesByTa($taId) {
        $stmt = $this->db->prepare("SELECT c.*, ct.assigned_at 
                                    FROM course_tas ct 
                                    JOIN courses c ON ct.course_id = c.id 
                                    WHERE ct.ta_id = ? 
                                    ORDER BY ct.assigned_at DESC");
        $stmt->bind_param("i", $taId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAvailableTAs($courseId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'ta' AND id NOT IN (
                                    SELECT ta_id FROM course_tas WHERE course_id = ?
                                ) ORDER BY full_name");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>