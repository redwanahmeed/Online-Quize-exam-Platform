<?php
require_once __DIR__ . '/../config/config.php';

class ReportModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Search students by ID or name
    public function searchStudents($keyword) {
        $keyword = "%{$keyword}%";
        $stmt = $this->conn->prepare("SELECT id, student_id, full_name, email, course, year_level, status 
                                      FROM students 
                                      WHERE student_id LIKE ? OR full_name LIKE ? 
                                      ORDER BY full_name ASC");
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get student by ID
    public function getStudentById($id) {
        $stmt = $this->conn->prepare("SELECT s.*, u.email as user_email 
                                      FROM students s 
                                      LEFT JOIN users u ON s.user_id = u.id 
                                      WHERE s.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get student by student_id
    public function getStudentByStudentId($student_id) {
        $stmt = $this->conn->prepare("SELECT s.*, u.email as user_email 
                                      FROM students s 
                                      LEFT JOIN users u ON s.user_id = u.id 
                                      WHERE s.student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get student's enrolled courses with grades
    public function getStudentCourses($student_id) {
        $stmt = $this->conn->prepare("SELECT c.course_code, c.course_name, c.credits, 
                                      e.enrollment_date, e.grade, e.status,
                                      CASE 
                                          WHEN e.grade = 'A+' THEN 4.00
                                          WHEN e.grade = 'A' THEN 4.00
                                          WHEN e.grade = 'A-' THEN 3.70
                                          WHEN e.grade = 'B+' THEN 3.30
                                          WHEN e.grade = 'B' THEN 3.00
                                          WHEN e.grade = 'B-' THEN 2.70
                                          WHEN e.grade = 'C+' THEN 2.30
                                          WHEN e.grade = 'C' THEN 2.00
                                          WHEN e.grade = 'D' THEN 1.00
                                          ELSE 0.00
                                      END as grade_point
                                      FROM enrollments e 
                                      JOIN courses c ON e.course_id = c.id 
                                      WHERE e.student_id = ? 
                                      ORDER BY e.enrollment_date DESC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get student's quiz attempts with all details
    public function getStudentQuizAttempts($student_id) {
        // Check if quiz_attempts table exists
        $result = $this->conn->query("SHOW TABLES LIKE 'quiz_attempts'");
        if ($result->num_rows == 0) {
            return [];
        }
        
        $stmt = $this->conn->prepare("SELECT qa.*, q.quiz_title, q.total_marks, q.quiz_type,
                                      q.passing_marks as passing_marks,
                                      (SELECT course_name FROM courses WHERE id = q.course_id) as course_name
                                      FROM quiz_attempts qa 
                                      LEFT JOIN quizzes q ON qa.quiz_id = q.id 
                                      WHERE qa.student_id = ? 
                                      ORDER BY qa.attempt_date DESC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $attempts = $result->fetch_all(MYSQLI_ASSOC);
        
        // Calculate additional fields if not present
        foreach ($attempts as &$attempt) {
            // Set passing marks if not set
            if (empty($attempt['passing_marks']) && isset($attempt['total_marks']) && $attempt['total_marks'] > 0) {
                $attempt['passing_marks'] = $attempt['total_marks'] * 0.4;
            }
            
            // Set percentage if not set
            if (empty($attempt['percentage']) && isset($attempt['obtained_marks']) && isset($attempt['total_marks']) && $attempt['total_marks'] > 0) {
                $attempt['percentage'] = ($attempt['obtained_marks'] / $attempt['total_marks']) * 100;
            }
        }
        
        return $attempts;
    }
    
    // Get quiz statistics with average, attempts, passing marks
    public function getQuizStatistics($student_id) {
        $attempts = $this->getStudentQuizAttempts($student_id);
        
        $stats = [
            'total_attempts' => count($attempts),
            'completed_attempts' => 0,
            'passed_attempts' => 0,
            'average_score' => 0,
            'highest_score' => 0,
            'lowest_score' => 100,
            'passing_rate' => 0,
            'quiz_details' => [], // Store per quiz details
            'by_type' => []
        ];
        
        $total_percentage = 0;
        $quiz_groups = [];
        
        // Group attempts by quiz
        foreach ($attempts as $attempt) {
            $quiz_id = $attempt['quiz_id'];
            if (!isset($quiz_groups[$quiz_id])) {
                $quiz_groups[$quiz_id] = [
                    'quiz_title' => $attempt['quiz_title'],
                    'course_name' => $attempt['course_name'],
                    'total_marks' => $attempt['total_marks'],
                    'passing_marks' => $attempt['passing_marks'],
                    'attempts_data' => [],
                    'attempt_count' => 0,
                    'total_percentage' => 0,
                    'best_score' => 0
                ];
            }
            
            if ($attempt['status'] == 'completed') {
                $percentage = $attempt['percentage'] ?? 0;
                $quiz_groups[$quiz_id]['attempts_data'][] = $attempt;
                $quiz_groups[$quiz_id]['attempt_count']++;
                $quiz_groups[$quiz_id]['total_percentage'] += $percentage;
                
                if ($percentage > $quiz_groups[$quiz_id]['best_score']) {
                    $quiz_groups[$quiz_id]['best_score'] = $percentage;
                }
            }
        }
        
        // Calculate per quiz average
        foreach ($quiz_groups as $quiz_id => &$group) {
            if ($group['attempt_count'] > 0) {
                $group['avg_score'] = round($group['total_percentage'] / $group['attempt_count'], 1);
            } else {
                $group['avg_score'] = 0;
            }
            $stats['quiz_details'][] = $group;
        }
        
        // Calculate overall stats
        foreach ($attempts as $attempt) {
            if ($attempt['status'] == 'completed') {
                $stats['completed_attempts']++;
                $percentage = $attempt['percentage'] ?? 0;
                $total_percentage += $percentage;
                
                // Check if passed
                $passing_marks = $attempt['passing_marks'] ?? ($attempt['total_marks'] * 0.4);
                $passing_percentage = ($passing_marks / $attempt['total_marks']) * 100;
                
                if ($percentage >= $passing_percentage) {
                    $stats['passed_attempts']++;
                }
                
                if ($percentage > $stats['highest_score']) {
                    $stats['highest_score'] = $percentage;
                }
                if ($percentage < $stats['lowest_score']) {
                    $stats['lowest_score'] = $percentage;
                }
                
                // Group by quiz type
                $type = $attempt['quiz_type'] ?? 'unknown';
                if (!isset($stats['by_type'][$type])) {
                    $stats['by_type'][$type] = ['count' => 0, 'total' => 0];
                }
                $stats['by_type'][$type]['count']++;
                $stats['by_type'][$type]['total'] += $percentage;
            }
        }
        
        if ($stats['completed_attempts'] > 0) {
            $stats['average_score'] = round($total_percentage / $stats['completed_attempts'], 2);
            $stats['passing_rate'] = round(($stats['passed_attempts'] / $stats['completed_attempts']) * 100, 1);
        }
        
        if ($stats['lowest_score'] == 100) {
            $stats['lowest_score'] = 0;
        }
        
        return $stats;
    }
    
    // Get overall GPA
    public function calculateGPA($student_id) {
        $courses = $this->getStudentCourses($student_id);
        
        $total_points = 0;
        $total_credits = 0;
        
        foreach ($courses as $course) {
            if ($course['status'] == 'completed' && $course['grade_point'] > 0) {
                $total_points += $course['grade_point'] * ($course['credits'] ?? 3);
                $total_credits += $course['credits'] ?? 3;
            }
        }
        
        if ($total_credits > 0) {
            return round($total_points / $total_credits, 2);
        }
        return 0;
    }
    
    // Get performance summary
    public function getPerformanceSummary($student_id) {
        $courses = $this->getStudentCourses($student_id);
        
        $summary = [
            'total_courses' => count($courses),
            'completed_courses' => 0,
            'in_progress_courses' => 0,
            'gpa' => 0,
            'total_credits' => 0,
            'earned_credits' => 0
        ];
        
        $total_grade_points = 0;
        $total_credits = 0;
        
        foreach ($courses as $course) {
            if ($course['status'] == 'completed' && $course['grade_point'] > 0) {
                $summary['completed_courses']++;
                $credits = $course['credits'] ?? 3;
                $total_grade_points += $course['grade_point'] * $credits;
                $total_credits += $credits;
                $summary['earned_credits'] += $credits;
            } elseif ($course['status'] == 'enrolled') {
                $summary['in_progress_courses']++;
                $summary['total_credits'] += $course['credits'] ?? 3;
            }
        }
        
        if ($total_credits > 0) {
            $summary['gpa'] = round($total_grade_points / $total_credits, 2);
        }
        
        return $summary;
    }
}
?>