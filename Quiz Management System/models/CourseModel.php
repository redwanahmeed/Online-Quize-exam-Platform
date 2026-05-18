<?php
require_once __DIR__ . '/../config/config.php';

class CourseModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Get all courses with instructor info
    public function getAllCourses() {
        $sql = "SELECT c.*, u.full_name as instructor_name 
                FROM courses c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                ORDER BY c.created_at DESC";
        $result = $this->conn->query($sql);
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // Get course by ID
    public function getCourseById($id) {
        $stmt = $this->conn->prepare("SELECT c.*, u.full_name as instructor_name 
                                      FROM courses c 
                                      LEFT JOIN users u ON c.instructor_id = u.id 
                                      WHERE c.id = ?");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get courses by status
    public function getCoursesByStatus($status) {
        $stmt = $this->conn->prepare("SELECT c.*, u.full_name as instructor_name 
                                      FROM courses c 
                                      LEFT JOIN users u ON c.instructor_id = u.id 
                                      WHERE c.status = ? 
                                      ORDER BY c.created_at DESC");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get courses by subject
    public function getCoursesBySubject($subject) {
        $stmt = $this->conn->prepare("SELECT c.*, u.full_name as instructor_name 
                                      FROM courses c 
                                      LEFT JOIN users u ON c.instructor_id = u.id 
                                      WHERE c.subject = ? 
                                      ORDER BY c.created_at DESC");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get courses by instructor
    public function getCoursesByInstructor($instructor_id) {
        $stmt = $this->conn->prepare("SELECT c.*, u.full_name as instructor_name 
                                      FROM courses c 
                                      LEFT JOIN users u ON c.instructor_id = u.id 
                                      WHERE c.instructor_id = ? 
                                      ORDER BY c.created_at DESC");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("i", $instructor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get filtered courses (Fixed version)
    public function getFilteredCourses($subject = null, $status = null, $instructor_id = null) {
        $sql = "SELECT c.*, u.full_name as instructor_name 
                FROM courses c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($subject && $subject != 'all') {
            $sql .= " AND c.subject = ?";
            $params[] = $subject;
            $types .= "s";
        }
        
        if ($status && $status != 'all') {
            $sql .= " AND c.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        if ($instructor_id && $instructor_id != 'all') {
            $sql .= " AND c.instructor_id = ?";
            $params[] = $instructor_id;
            $types .= "i";
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        // Check if prepare failed
        if (!$stmt) {
            error_log("SQL Prepare Error: " . $this->conn->error);
            return [];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    // Get all subjects (unique)
    public function getAllSubjects() {
        $result = $this->conn->query("SELECT DISTINCT subject FROM courses WHERE subject IS NOT NULL AND subject != '' ORDER BY subject");
        if ($result) {
            $subjects = $result->fetch_all(MYSQLI_ASSOC);
            return array_column($subjects, 'subject');
        }
        return ['Computer Science', 'Software Engineering', 'Data Science', 'Information Technology'];
    }
    
    // Get all instructors
    public function getAllInstructors() {
        $stmt = $this->conn->prepare("SELECT id, full_name FROM users WHERE role IN ('instructor', 'teaching_assistant') AND status = 'active' ORDER BY full_name");
        if (!$stmt) {
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Add course
    public function addCourse($course_code, $course_name, $subject, $description, $credits, $instructor_id, $status = 'draft') {
        $stmt = $this->conn->prepare("INSERT INTO courses (course_code, course_name, subject, description, credits, instructor_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("sssssis", $course_code, $course_name, $subject, $description, $credits, $instructor_id, $status);
        return $stmt->execute();
    }
    
    // Update course
    public function updateCourse($id, $course_code, $course_name, $subject, $description, $credits, $instructor_id, $status) {
        $stmt = $this->conn->prepare("UPDATE courses SET course_code = ?, course_name = ?, subject = ?, description = ?, credits = ?, instructor_id = ?, status = ? WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("sssssisi", $course_code, $course_name, $subject, $description, $credits, $instructor_id, $status, $id);
        return $stmt->execute();
    }
    
    // Update course status only
    public function updateCourseStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE courses SET status = ? WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    
    // Delete course
    public function deleteCourse($id) {
        $stmt = $this->conn->prepare("DELETE FROM courses WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Get course statistics
    public function getCourseStats() {
        $stats = ['total' => 0, 'active' => 0, 'draft' => 0, 'archived' => 0];
        
        $result = $this->conn->query("SELECT COUNT(*) as total FROM courses");
        if ($result) {
            $stats['total'] = $result->fetch_assoc()['total'];
        }
        
        $result = $this->conn->query("SELECT COUNT(*) as active FROM courses WHERE status = 'active'");
        if ($result) {
            $stats['active'] = $result->fetch_assoc()['active'];
        }
        
        $result = $this->conn->query("SELECT COUNT(*) as draft FROM courses WHERE status = 'draft'");
        if ($result) {
            $stats['draft'] = $result->fetch_assoc()['draft'];
        }
        
        $result = $this->conn->query("SELECT COUNT(*) as archived FROM courses WHERE status = 'archived'");
        if ($result) {
            $stats['archived'] = $result->fetch_assoc()['archived'];
        }
        
        return $stats;
    }
    
    // Get enrollments count for a course
    public function getEnrollmentCount($course_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?");
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['count'] ?? 0;
    }
    
    // Check if course code exists
    public function courseCodeExists($course_code, $exclude_id = null) {
        if ($exclude_id) {
            $stmt = $this->conn->prepare("SELECT id FROM courses WHERE course_code = ? AND id != ?");
            $stmt->bind_param("si", $course_code, $exclude_id);
        } else {
            $stmt = $this->conn->prepare("SELECT id FROM courses WHERE course_code = ?");
            $stmt->bind_param("s", $course_code);
        }
        if (!$stmt) {
            return false;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
   // Search courses - updated version
public function searchCourses($keyword) {
    $keyword = "%{$keyword}%";
    $stmt = $this->conn->prepare("SELECT c.*, u.full_name as instructor_name,
                                  (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrollment_count
                                  FROM courses c 
                                  LEFT JOIN users u ON c.instructor_id = u.id 
                                  WHERE c.course_code LIKE ? 
                                     OR c.course_name LIKE ? 
                                     OR c.subject LIKE ?
                                     OR c.description LIKE ?
                                  ORDER BY c.created_at DESC");
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
}
?>