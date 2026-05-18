<?php
require_once __DIR__ . '/../config/config.php';

class AuditLogModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Add log entry
    public function addLog($user_id, $action, $action_type, $target_type, $target_id, $target_name, $details = null) {
        // Get user info
        $user = null;
        $username = '';
        $user_role = '';
        
        if ($user_id) {
            $stmt = $this->conn->prepare("SELECT username, role FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if ($user) {
                $username = $user['username'];
                $user_role = $user['role'];
            }
        }
        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $this->conn->prepare("INSERT INTO audit_logs (user_id, username, user_role, action, action_type, target_type, target_id, target_name, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssissss", $user_id, $username, $user_role, $action, $action_type, $target_type, $target_id, $target_name, $details, $ip_address, $user_agent);
        return $stmt->execute();
    }
    
    // Get all logs with filters
    public function getLogs($filters = []) {
        $sql = "SELECT * FROM audit_logs WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }
        
        if (!empty($filters['action_type'])) {
            $sql .= " AND action_type = ?";
            $params[] = $filters['action_type'];
            $types .= "s";
        }
        
        if (!empty($filters['target_type'])) {
            $sql .= " AND target_type = ?";
            $params[] = $filters['target_type'];
            $types .= "s";
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (action LIKE ? OR target_name LIKE ? OR username LIKE ? OR details LIKE ?)";
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $types .= "ssss";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
            $types .= "i";
        }
        
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
    
    // Get log by ID
    public function getLogById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM audit_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get statistics
    public function getStatistics() {
        $stats = [];
        
        // Total logs
        $result = $this->conn->query("SELECT COUNT(*) as total FROM audit_logs");
        $stats['total'] = $result->fetch_assoc()['total'];
        
        // Today's logs
        $result = $this->conn->query("SELECT COUNT(*) as today FROM audit_logs WHERE DATE(created_at) = CURDATE()");
        $stats['today'] = $result->fetch_assoc()['today'];
        
        // By action type
        $result = $this->conn->query("SELECT action_type, COUNT(*) as count FROM audit_logs GROUP BY action_type");
        $stats['by_action'] = $result->fetch_all(MYSQLI_ASSOC);
        
        // By target type
        $result = $this->conn->query("SELECT target_type, COUNT(*) as count FROM audit_logs GROUP BY target_type");
        $stats['by_target'] = $result->fetch_all(MYSQLI_ASSOC);
        
        // Last 7 days activity
        $result = $this->conn->query("SELECT DATE(created_at) as date, COUNT(*) as count 
                                      FROM audit_logs 
                                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                      GROUP BY DATE(created_at)");
        $stats['weekly'] = $result->fetch_all(MYSQLI_ASSOC);
        
        return $stats;
    }
    
    // Get recent logs for dashboard
    public function getRecentLogs($limit = 10) {
        $stmt = $this->conn->prepare("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get action types
    public function getActionTypes() {
        return ['create', 'update', 'delete', 'status_change', 'role_change', 'approve', 'login', 'logout'];
    }
    
    // Get target types
    public function getTargetTypes() {
        return ['user', 'course', 'student', 'quiz', 'announcement', 'instructor_request'];
    }
    
    // Get users list for filter
    public function getUsers() {
        $result = $this->conn->query("SELECT id, username, role FROM users ORDER BY username");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Clear old logs (older than days)
    public function clearOldLogs($days = 90) {
        $stmt = $this->conn->prepare("DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->bind_param("i", $days);
        return $stmt->execute();
    }
}
?>