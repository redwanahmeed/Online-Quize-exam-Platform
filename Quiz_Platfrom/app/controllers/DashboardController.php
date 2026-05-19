<?php
class DashboardController extends Controller {
    
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        // Get user information
        $userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $userStmt->bind_param("i", $_SESSION['user_id']);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $user = $userResult->fetch_assoc();
        
        if (!$user) {
            session_destroy();
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        // ========== সব কোর্স দেখাবে (সব ইউজারের জন্য) ==========
        $allCoursesStmt = $db->prepare("SELECT c.*, u.full_name as instructor_name 
                                        FROM courses c 
                                        JOIN users u ON c.instructor_id = u.id 
                                        ORDER BY c.created_at DESC");
        $allCoursesStmt->execute();
        $allCoursesResult = $allCoursesStmt->get_result();
        $allCourses = $allCoursesResult->fetch_all(MYSQLI_ASSOC);
        
        // ========== প্রতিটি কোর্সের জন্য এনরোল্ড স্টুডেন্ট কাউন্ট ==========
        foreach ($allCourses as &$course) {
            $countStmt = $db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ? AND status = 'approved'");
            $countStmt->bind_param("i", $course['id']);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $countData = $countResult->fetch_assoc();
            $course['enrolled_students'] = $countData['count'];
        }
        
        // ========== ইউজারের নিজের কোর্স (instructor হলে) ==========
        $myCourses = [];
        $myCoursesWithCount = [];
        if ($user['role'] == 'instructor') {
            $myCoursesStmt = $db->prepare("SELECT * FROM courses WHERE instructor_id = ? ORDER BY created_at DESC");
            $myCoursesStmt->bind_param("i", $_SESSION['user_id']);
            $myCoursesStmt->execute();
            $myCoursesResult = $myCoursesStmt->get_result();
            $myCourses = $myCoursesResult->fetch_all(MYSQLI_ASSOC);
            
            // প্রতিটি নিজের কোর্সের জন্য এনরোল্ড স্টুডেন্ট কাউন্ট
            foreach ($myCourses as &$course) {
                $countStmt = $db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ? AND status = 'approved'");
                $countStmt->bind_param("i", $course['id']);
                $countStmt->execute();
                $countResult = $countStmt->get_result();
                $countData = $countResult->fetch_assoc();
                $course['enrolled_students'] = $countData['count'];
            }
            $myCoursesWithCount = $myCourses;
        }
        
        // ========== ইউজারের এনরোল্ড কোর্স (student হলে) ==========
        $enrolledCourses = [];
        if ($user['role'] != 'instructor') {
            $enrolledStmt = $db->prepare("SELECT c.*, e.enrolled_at, e.status as enrollment_status 
                                          FROM courses c 
                                          JOIN enrollments e ON c.id = e.course_id 
                                          WHERE e.student_id = ? AND e.status = 'approved'
                                          ORDER BY e.enrolled_at DESC");
            $enrolledStmt->bind_param("i", $_SESSION['user_id']);
            $enrolledStmt->execute();
            $enrolledResult = $enrolledStmt->get_result();
            $enrolledCourses = $enrolledResult->fetch_all(MYSQLI_ASSOC);
        }
        
        // ========== স্ট্যাটিস্টিক্স ক্যালকুলেশন ==========
        $stats = [
            'total_courses' => count($allCourses),
            'my_courses' => count($myCourses),
            'enrolled_courses' => count($enrolledCourses),
            'total_students' => 0,
            'total_quizzes' => 0,
            'user_role' => $user['role']
        ];
        
        // সব কোর্সের মোট শিক্ষার্থী সংখ্যা (instructor এর জন্য)
        if ($user['role'] == 'instructor') {
            foreach ($myCourses as $course) {
                $studentStmt = $db->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ? AND status = 'approved'");
                $studentStmt->bind_param("i", $course['id']);
                $studentStmt->execute();
                $studentResult = $studentStmt->get_result();
                $studentData = $studentResult->fetch_assoc();
                $stats['total_students'] += $studentData['count'];
                
                // কুইজ কাউন্ট
                $quizStmt = $db->prepare("SELECT COUNT(*) as count FROM quizzes WHERE course_id = ?");
                $quizStmt->bind_param("i", $course['id']);
                $quizStmt->execute();
                $quizResult = $quizStmt->get_result();
                $quizData = $quizResult->fetch_assoc();
                $stats['total_quizzes'] += $quizData['count'];
            }
        }
        
        // পেন্ডিং এনরোলমেন্ট রিকোয়েস্ট (instructor এর জন্য)
        $pendingRequests = [];
        if ($user['role'] == 'instructor') {
            $pendingStmt = $db->prepare("SELECT e.*, u.full_name as student_name, c.title as course_title 
                                        FROM enrollments e 
                                        JOIN users u ON e.student_id = u.id 
                                        JOIN courses c ON e.course_id = c.id 
                                        WHERE c.instructor_id = ? AND e.status = 'pending'
                                        ORDER BY e.enrolled_at DESC LIMIT 5");
            $pendingStmt->bind_param("i", $_SESSION['user_id']);
            $pendingStmt->execute();
            $pendingResult = $pendingStmt->get_result();
            $pendingRequests = $pendingResult->fetch_all(MYSQLI_ASSOC);
        }
        
        // Pass data to view
        parent::view('dashboard/index', [
            'user' => $user,
            'allCourses' => $allCourses,
            'myCourses' => $myCoursesWithCount,
            'enrolledCourses' => $enrolledCourses,
            'stats' => $stats,
            'pendingRequests' => $pendingRequests
        ]);
    }
}
?>