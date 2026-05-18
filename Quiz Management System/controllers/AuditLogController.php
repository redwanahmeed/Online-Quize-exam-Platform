<?php
require_once __DIR__ . '/../models/AuditLogModel.php';

class AuditLogController {
    private $auditLogModel;
    
    public function __construct() {
        $this->auditLogModel = new AuditLogModel();
    }
    
    public function index() {
        requireRole('admin');
        
        $filters = [];
        
        // Get filter parameters
        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = (int)$_GET['user_id'];
        }
        if (!empty($_GET['action_type'])) {
            $filters['action_type'] = $_GET['action_type'];
        }
        if (!empty($_GET['target_type'])) {
            $filters['target_type'] = $_GET['target_type'];
        }
        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        // Limit
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $filters['limit'] = $limit;
        
        $logs = $this->auditLogModel->getLogs($filters);
        $stats = $this->auditLogModel->getStatistics();
        $actionTypes = $this->auditLogModel->getActionTypes();
        $targetTypes = $this->auditLogModel->getTargetTypes();
        $users = $this->auditLogModel->getUsers();
        
        include __DIR__ . '/../views/audit/index.php';
    }
    
    public function view() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $log = $this->auditLogModel->getLogById($id);
        
        if (!$log) {
            header('Location: index.php?page=audit&error=Log not found');
            exit();
        }
        
        include __DIR__ . '/../views/audit/view.php';
    }
    
    public function clear() {
        requireRole('admin');
        
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 90;
        
        if ($this->auditLogModel->clearOldLogs($days)) {
            header('Location: index.php?page=audit&success=Old logs cleared successfully');
            exit();
        } else {
            header('Location: index.php?page=audit&error=Failed to clear logs');
            exit();
        }
    }
    
    // Get recent logs for AJAX
    public function getRecent() {
        requireRole('admin');
        
        header('Content-Type: application/json');
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $logs = $this->auditLogModel->getRecentLogs($limit);
        
        echo json_encode(['status' => 'success', 'logs' => $logs]);
        exit();
    }
}
?>