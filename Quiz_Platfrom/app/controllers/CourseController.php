<?php
class CourseController extends Controller {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            die("Database connection failed!");
        }
        
        // সব course দেখাবে
        $stmt = $db->prepare("SELECT * FROM courses ORDER BY created_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $allCourses = $result->fetch_all(MYSQLI_ASSOC);
        
        // আলাদা আলাদা status অনুযায়ী course আলাদা করা
        $publishedCourses = [];
        $draftCourses = [];
        $archivedCourses = [];
        
        foreach ($allCourses as $course) {
            if ($course['status'] == 'published') {
                $publishedCourses[] = $course;
            } elseif ($course['status'] == 'draft') {
                $draftCourses[] = $course;
            } elseif ($course['status'] == 'archived') {
                $archivedCourses[] = $course;
            }
        }
        
        // Get student count for each course
        foreach ($allCourses as &$course) {
            $countStmt = $db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ? AND status = 'approved'");
            $countStmt->bind_param("i", $course['id']);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $countData = $countResult->fetch_assoc();
            $course['student_count'] = $countData['count'];
        }
        
        // সব ভেরিয়েবল ভিউতে পাঠানো হচ্ছে
        parent::view('courses/index', [
            'courses' => $allCourses,
            'publishedCourses' => $publishedCourses,
            'draftCourses' => $draftCourses,
            'archivedCourses' => $archivedCourses,
            'courseModel' => $this
        ]);
    }
    
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            
            $stmt = $db->prepare("INSERT INTO courses (instructor_id, title, subject, description, enrollment_type, max_students, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssis", 
                $_SESSION['user_id'],
                $_POST['title'],
                $_POST['subject'],
                $_POST['description'],
                $_POST['enrollment_type'],
                $_POST['max_students'],
                $_POST['status']
            );
            
            if ($stmt->execute()) {
                header("Location: index.php?controller=course&action=index&success=created");
                exit();
            } else {
                $error = "Failed to create course: " . $db->error;
                parent::view('courses/create', ['error' => $error]);
            }
        } else {
            parent::view('courses/create');
        }
    }
    
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $id = $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();
        
        if (!$course) {
            header("Location: index.php?controller=course&action=index");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateStmt = $db->prepare("UPDATE courses SET title=?, subject=?, description=?, enrollment_type=?, max_students=?, status=? WHERE id=?");
            $updateStmt->bind_param("ssssisi", 
                $_POST['title'],
                $_POST['subject'],
                $_POST['description'],
                $_POST['enrollment_type'],
                $_POST['max_students'],
                $_POST['status'],
                $id
            );
            
            if ($updateStmt->execute()) {
                header("Location: index.php?controller=course&action=index&success=updated");
                exit();
            } else {
                $error = "Failed to update course";
                parent::view('courses/edit', ['course' => $course, 'error' => $error]);
            }
        } else {
            parent::view('courses/edit', ['course' => $course]);
        }
    }
    
    public function archive() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $id = $_GET['id'];
        $stmt = $db->prepare("UPDATE courses SET status='archived' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        header("Location: index.php?controller=course&action=index&success=archived");
    }
    
    public function publish() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $id = $_GET['id'];
        $stmt = $db->prepare("UPDATE courses SET status='published' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        header("Location: index.php?controller=course&action=index&success=published");
    }
    
    public function unpublish() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $id = $_GET['id'];
        $stmt = $db->prepare("UPDATE courses SET status='draft' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        header("Location: index.php?controller=course&action=index&success=unpublished");
    }
    
    public function students() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $courseId = $_GET['id'];
        
        $courseStmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $courseStmt->bind_param("i", $courseId);
        $courseStmt->execute();
        $courseResult = $courseStmt->get_result();
        $course = $courseResult->fetch_assoc();
        
        if (!$course) {
            header("Location: index.php?controller=course&action=index");
            exit();
        }
        
        // Get enrolled students
        $studentStmt = $db->prepare("SELECT u.*, e.enrolled_at FROM enrollments e JOIN users u ON e.student_id = u.id WHERE e.course_id = ? AND e.status = 'approved' ORDER BY e.enrolled_at DESC");
        $studentStmt->bind_param("i", $courseId);
        $studentStmt->execute();
        $studentResult = $studentStmt->get_result();
        $students = $studentResult->fetch_all(MYSQLI_ASSOC);
        
        // Get pending count
        $pendingStmt = $db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ? AND status = 'pending'");
        $pendingStmt->bind_param("i", $courseId);
        $pendingStmt->execute();
        $pendingResult = $pendingStmt->get_result();
        $pendingData = $pendingResult->fetch_assoc();
        $pendingCount = $pendingData['count'];
        
        parent::view('courses/students', [
            'course' => $course,
            'students' => $students,
            'pendingCount' => $pendingCount,
            'enrolledCount' => count($students)
        ]);
    }
    
    // Helper method to get enrolled count (for view)
    public function getEnrolledCount($courseId) {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ? AND status = 'approved'");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['count'];
    }
}
?>