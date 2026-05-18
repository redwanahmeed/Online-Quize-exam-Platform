<?php
require_once __DIR__ . '/../config/config.php';

class StudentModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Get all students with details
    public function getAllStudentsWithDetails() {
        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM enrollments WHERE student_id = s.id) as total_courses
                FROM students s 
                ORDER BY s.created_at DESC";
        $result = $this->conn->query($sql);
        $students = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($students as &$student) {
            $student['gpa'] = $this->calculateGPA($student['id']);
        }
        
        return $students;
    }
    
    // Calculate GPA for a student
    private function calculateGPA($student_id) {
        $grade_points = [
            'A+' => 4.00,
            'A' => 4.00,
            'A-' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'B-' => 2.70,
            'C+' => 2.30,
            'C' => 2.00,
            'D' => 1.00,
            'F' => 0.00
        ];
        
        $stmt = $this->conn->prepare("SELECT grade FROM enrollments WHERE student_id = ? AND grade IS NOT NULL AND grade != ''");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $grades = $result->fetch_all(MYSQLI_ASSOC);
        
        if (count($grades) == 0) {
            return null;
        }
        
        $total_points = 0;
        $total_courses = 0;
        
        foreach ($grades as $grade) {
            $letter_grade = $grade['grade'];
            if (isset($grade_points[$letter_grade])) {
                $total_points += $grade_points[$letter_grade];
                $total_courses++;
            }
        }
        
        if ($total_courses > 0) {
            return round($total_points / $total_courses, 2);
        }
        
        return null;
    }
    
    // Get student with details by student id
    public function getStudentWithDetails($id) {
        $stmt = $this->conn->prepare("SELECT s.*, 
                                      (SELECT COUNT(*) FROM enrollments WHERE student_id = s.id) as total_courses
                                      FROM students s WHERE s.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        if ($student) {
            $student['gpa'] = $this->calculateGPA($id);
            $student['courses'] = $this->getStudentCourses($id);
        }
        
        return $student;
    }
    
    // Get student courses
    public function getStudentCourses($student_id) {
        $stmt = $this->conn->prepare("SELECT c.*, e.enrollment_date, e.grade, e.status 
                                      FROM enrollments e 
                                      JOIN courses c ON e.course_id = c.id 
                                      WHERE e.student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get all students (simple version)
    public function getAllStudents() {
        $result = $this->conn->query("SELECT * FROM students ORDER BY created_at DESC");
        $students = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($students as &$student) {
            $student['gpa'] = $this->calculateGPA($student['id']);
        }
        
        return $students;
    }
    
    // Get student by ID
    public function getStudentById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get student by student_id
    public function getStudentByStudentId($student_id) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get student by user_id
    public function getStudentByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Delete student by user_id
    public function deleteStudentByUserId($user_id) {
        $student = $this->getStudentByUserId($user_id);
        if ($student) {
            $stmt = $this->conn->prepare("DELETE FROM enrollments WHERE student_id = ?");
            $stmt->bind_param("i", $student['id']);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("DELETE FROM students WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            return $stmt->execute();
        }
        return true;
    }
    
    // Add student
    public function addStudent($student_id, $full_name, $email, $phone, $address, $course, $year_level, $user_id = null) {
        if ($user_id === null) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $user_id = $user ? $user['id'] : null;
        }
        
        $stmt = $this->conn->prepare("INSERT INTO students (user_id, student_id, full_name, email, phone, address, course, year_level, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("issssssi", $user_id, $student_id, $full_name, $email, $phone, $address, $course, $year_level);
        return $stmt->execute();
    }
    
    // Update student
    public function updateStudent($id, $full_name, $email, $phone, $address, $course, $year_level, $status) {
        $stmt = $this->conn->prepare("UPDATE students SET full_name = ?, email = ?, phone = ?, address = ?, course = ?, year_level = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $full_name, $email, $phone, $address, $course, $year_level, $status, $id);
        return $stmt->execute();
    }
    
    // Update student status
    public function updateStudentStatus($student_id, $status) {
        $stmt = $this->conn->prepare("UPDATE students SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $student_id);
        return $stmt->execute();
    }
    
    // Update student status by user_id
    public function updateStudentStatusByUserId($user_id, $status) {
        $stmt = $this->conn->prepare("UPDATE students SET status = ? WHERE user_id = ?");
        $stmt->bind_param("si", $status, $user_id);
        return $stmt->execute();
    }
    
    // Delete student
    public function deleteStudent($id) {
        $stmt = $this->conn->prepare("DELETE FROM enrollments WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $stmt = $this->conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Search students
    public function searchStudents($keyword) {
        $keyword = "%{$keyword}%";
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE full_name LIKE ? OR student_id LIKE ? OR email LIKE ? OR course LIKE ?");
        $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        $students = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($students as &$student) {
            $student['gpa'] = $this->calculateGPA($student['id']);
        }
        
        return $students;
    }
    
    // Get students by course
    public function getStudentsByCourse($course) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE course = ? AND status = 'active' ORDER BY full_name");
        $stmt->bind_param("s", $course);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get students by year level
    public function getStudentsByYearLevel($year_level) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE year_level = ? AND status = 'active' ORDER BY full_name");
        $stmt->bind_param("i", $year_level);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get student statistics
    public function getStats() {
        $stats = [];
        
        $result = $this->conn->query("SELECT COUNT(*) as total FROM students");
        $stats['total'] = $result->fetch_assoc()['total'];
        
        $result = $this->conn->query("SELECT COUNT(*) as active FROM students WHERE status = 'active'");
        $stats['active'] = $result->fetch_assoc()['active'];
        
        $result = $this->conn->query("SELECT COUNT(*) as inactive FROM students WHERE status = 'inactive'");
        $stats['inactive'] = $result->fetch_assoc()['inactive'];
        
        return $stats;
    }
    
    // Get recent students
    public function getRecentStudents($limit = 5) {
        $stmt = $this->conn->prepare("SELECT * FROM students ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // Get filtered students
public function getFilteredStudents($course = null, $year_level = null, $status = null) {
    $sql = "SELECT * FROM students WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($course && $course != 'all') {
        $sql .= " AND course = ?";
        $params[] = $course;
        $types .= "s";
    }
    
    if ($year_level && $year_level != 'all') {
        $sql .= " AND year_level = ?";
        $params[] = $year_level;
        $types .= "i";
    }
    
    if ($status && $status != 'all') {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
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
}
?>