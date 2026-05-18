<?php
require_once __DIR__ . '/../models/AnnouncementModel.php';

class AnnouncementController {
    private $announcementModel;
    
    public function __construct() {
        $this->announcementModel = new AnnouncementModel();
    }
    
  
    public function index() {
        requireRole('admin');
        
        $type = $_GET['type'] ?? 'all';
        $target_role = $_GET['target_role'] ?? 'all';
        $status = $_GET['status'] ?? 'all';
        
        $announcements = $this->announcementModel->getFilteredAnnouncements($type, $target_role, $status);
        $stats = $this->announcementModel->getStats();
        $announcementTypes = $this->announcementModel->getAnnouncementTypes();
        $targetRoles = $this->announcementModel->getTargetRoles();
        
        include __DIR__ . '/../views/announcements/index.php';
    }
    
    public function getUserAnnouncements() {
        requireAuth();
        
        $user_role = $_SESSION['role'];
        $announcements = $this->announcementModel->getActiveAnnouncements($user_role);
        $unreadCount = $this->announcementModel->getUnreadCount($_SESSION['user_id'], $user_role);
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'announcements' => $announcements,
            'unread_count' => $unreadCount
        ]);
        exit();
    }
    

    public function view() {
        requireAuth();
        
        $id = (int)$_GET['id'];
        $announcement = $this->announcementModel->getAnnouncementById($id);
        
        if (!$announcement) {
            header('Location: index.php?page=dashboard&error=Announcement not found');
            exit();
        }
        
        // Mark as viewed
        $this->announcementModel->markAsViewed($id, $_SESSION['user_id']);
        
        include __DIR__ . '/../views/announcements/view.php';
    }
    
   
    public function create() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = sanitize($_POST['title']);
            $content = sanitize($_POST['content']);
            $announcement_type = sanitize($_POST['announcement_type']);
            $target_role = sanitize($_POST['target_role']);
            $priority = sanitize($_POST['priority']);
            $status = sanitize($_POST['status']);
            $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
            $created_by = $_SESSION['user_id'];
            
            // Validation
            if (empty($title) || empty($content)) {
                header('Location: index.php?page=announcements&action=create&error=Title and content are required');
                exit();
            }
            
            if ($this->announcementModel->addAnnouncement($title, $content, $announcement_type, $target_role, $priority, $status, $created_by, $expires_at)) {
                header('Location: index.php?page=announcements&success=Announcement created successfully');
                exit();
            } else {
                header('Location: index.php?page=announcements&action=create&error=Failed to create announcement');
                exit();
            }
        }
        
        $announcementTypes = $this->announcementModel->getAnnouncementTypes();
        $targetRoles = $this->announcementModel->getTargetRoles();
        $priorities = $this->announcementModel->getPriorities();
        
        include __DIR__ . '/../views/announcements/create.php';
    }
    
  
    public function edit() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $announcement = $this->announcementModel->getAnnouncementById($id);
        
        if (!$announcement) {
            header('Location: index.php?page=announcements&error=Announcement not found');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = sanitize($_POST['title']);
            $content = sanitize($_POST['content']);
            $announcement_type = sanitize($_POST['announcement_type']);
            $target_role = sanitize($_POST['target_role']);
            $priority = sanitize($_POST['priority']);
            $status = sanitize($_POST['status']);
            $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
            
            if ($this->announcementModel->updateAnnouncement($id, $title, $content, $announcement_type, $target_role, $priority, $status, $expires_at)) {
                header('Location: index.php?page=announcements&success=Announcement updated successfully');
                exit();
            } else {
                header('Location: index.php?page=announcements&action=edit&id=' . $id . '&error=Failed to update announcement');
                exit();
            }
        }
        
        $announcementTypes = $this->announcementModel->getAnnouncementTypes();
        $targetRoles = $this->announcementModel->getTargetRoles();
        $priorities = $this->announcementModel->getPriorities();
        
        include __DIR__ . '/../views/announcements/edit.php';
    }
    
  
    public function delete() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        
        if ($this->announcementModel->deleteAnnouncement($id)) {
            header('Location: index.php?page=announcements&success=Announcement deleted successfully');
            exit();
        } else {
            header('Location: index.php?page=announcements&error=Failed to delete announcement');
            exit();
        }
    }
    
  
    public function updateStatus() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            $id = (int)$_POST['announcement_id'];
            $new_status = sanitize($_POST['status']);
            
            $valid_status = ['active', 'inactive', 'expired'];
            if (!in_array($new_status, $valid_status)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
                exit();
            }
            
            if ($this->announcementModel->updateStatus($id, $new_status)) {
                echo json_encode(['status' => 'success', 'message' => 'Announcement status updated']);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
                exit();
            }
        }
    }
    
    public function search() {
        requireRole('admin');
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
            $keyword = sanitize($_POST['search']);
            
            if (strlen($keyword) < 2) {
                echo json_encode(['status' => 'error', 'message' => 'Search keyword must be at least 2 characters']);
                exit();
            }
            
            $announcements = $this->announcementModel->searchAnnouncements($keyword);
            echo json_encode(['status' => 'success', 'announcements' => $announcements]);
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'No search keyword provided']);
        exit();
    }
    
    
    public function markAsRead() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            $announcement_id = (int)$_POST['announcement_id'];
            
            if ($this->announcementModel->markAsViewed($announcement_id, $_SESSION['user_id'])) {
                echo json_encode(['status' => 'success', 'message' => 'Marked as read']);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to mark as read']);
                exit();
            }
        }
    }
    
    
    public function getUnreadCount() {
        requireAuth();
        
        header('Content-Type: application/json');
        
        $user_role = $_SESSION['role'];
        $unreadCount = $this->announcementModel->getUnreadCount($_SESSION['user_id'], $user_role);
        
        echo json_encode([
            'status' => 'success',
            'unread_count' => $unreadCount
        ]);
        exit();
    }
}
?>