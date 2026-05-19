<?php
require_once "../core/Controller.php";
require_once "../app/models/Enrollment.php";
require_once "../app/models/Course.php";

class EnrollmentController extends Controller {
    
    public function requests() {
        $this->isLoggedIn();
        $this->isInstructor();
        
        $courseId = $_GET['course_id'];
        $enrollmentModel = new Enrollment();
        $requests = $enrollmentModel->getPendingRequests($courseId);
        
        $this->view('enrollments/requests', ['requests' => $requests, 'course_id' => $courseId]);
    }
    
    public function approve() {
        $this->isLoggedIn();
        $this->isInstructor();
        
        $enrollmentId = $_GET['id'];
        $courseId = $_GET['course_id'];
        $enrollmentModel = new Enrollment();
        
        if ($enrollmentModel->updateStatus($enrollmentId, 'approved')) {
            header("Location: index.php?controller=enrollment&action=requests&course_id={$courseId}&success=approved");
        } else {
            header("Location: index.php?controller=enrollment&action=requests&course_id={$courseId}&error=failed");
        }
    }
    
    public function reject() {
        $this->isLoggedIn();
        $this->isInstructor();
        
        $enrollmentId = $_GET['id'];
        $courseId = $_GET['course_id'];
        $enrollmentModel = new Enrollment();
        
        if ($enrollmentModel->updateStatus($enrollmentId, 'rejected')) {
            header("Location: index.php?controller=enrollment&action=requests&course_id={$courseId}&success=rejected");
        } else {
            header("Location: index.php?controller=enrollment&action=requests&course_id={$courseId}&error=failed");
        }
    }
}
?>