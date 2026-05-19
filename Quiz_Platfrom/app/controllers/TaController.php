<?php
class TaController extends Controller {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $taModel = $this->model('Ta');
        $allTAs = $taModel->getAllTAs();
        
        // Debug: Check if TAs exist
        if (empty($allTAs)) {
            echo "<!-- No TAs found in database -->";
        }
        
        $this->view('ta/index', ['allTAs' => $allTAs]);
    }
    
    public function assign() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        
        if ($courseId == 0) {
            header("Location: index.php?controller=course&action=index");
            exit();
        }
        
        $taModel = $this->model('Ta');
        $courseModel = $this->model('Course');
        
        $course = $courseModel->find($courseId);
        
        if (!$course) {
            header("Location: index.php?controller=course&action=index");
            exit();
        }
        
        $assignedTAs = $taModel->getTAsByCourse($courseId);
        $availableTAs = $taModel->getAvailableTAs($courseId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taId = (int)$_POST['ta_id'];
            
            if ($taModel->assignTaToCourse($courseId, $taId)) {
                header("Location: index.php?controller=ta&action=assign&course_id={$courseId}&success=assigned");
                exit();
            } else {
                $error = "TA already assigned to this course!";
            }
        }
        
        $this->view('ta/assign', [
            'course' => $course,
            'assignedTAs' => $assignedTAs,
            'availableTAs' => $availableTAs,
            'error' => isset($error) ? $error : null
        ]);
    }
    
    public function remove() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        $taId = isset($_GET['ta_id']) ? (int)$_GET['ta_id'] : 0;
        
        $taModel = $this->model('Ta');
        $taModel->removeTaFromCourse($courseId, $taId);
        
        header("Location: index.php?controller=ta&action=assign&course_id={$courseId}&success=removed");
    }
}
?>