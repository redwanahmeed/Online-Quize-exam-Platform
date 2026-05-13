<?php
require_once __DIR__ . '/../config/config.php';

class CourseModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Get all courses
    public function getAllCourses() {
        $result = $this->conn->query("SELECT * FROM courses ORDER BY course_code");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get course by ID
    public function getCourseById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Add course
    public function addCourse($course_code, $course_name, $description, $credits) {
        $stmt = $this->conn->prepare("INSERT INTO courses (course_code, course_name, description, credits) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $course_code, $course_name, $description, $credits);
        return $stmt->execute();
    }
    
    // Update course
    public function updateCourse($id, $course_code, $course_name, $description, $credits, $status) {
        $stmt = $this->conn->prepare("UPDATE courses SET course_code = ?, course_name = ?, description = ?, credits = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $course_code, $course_name, $description, $credits, $status, $id);
        return $stmt->execute();
    }
    
    // Delete course
    public function deleteCourse($id) {
        $stmt = $this->conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>