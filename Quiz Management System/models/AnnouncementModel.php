<?php
require_once __DIR__ . '/../config/config.php';

class AnnouncementModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Get all announcements (for admin)
    public function getAllAnnouncements() {
        $sql = "SELECT a.*, u.full_name as created_by_name,
                (SELECT COUNT(*) FROM announcement_views WHERE announcement_id = a.id) as view_count
                FROM announcements a 
                LEFT JOIN users u ON a.created_by = u.id 
                ORDER BY a.priority = 'urgent' DESC, 
                         a.priority = 'high' DESC,
                         a.created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get active announcements for users (based on role)
    public function getActiveAnnouncements($user_role = null) {
        $sql = "SELECT a.*, u.full_name as created_by_name,
                (SELECT COUNT(*) FROM announcement_views WHERE announcement_id = a.id) as view_count
                FROM announcements a 
                LEFT JOIN users u ON a.created_by = u.id 
                WHERE a.status = 'active' 
                AND (a.expires_at IS NULL OR a.expires_at >= CURDATE())
                AND (a.target_role = 'all' OR a.target_role = ?)
                ORDER BY a.priority = 'urgent' DESC, 
                         a.priority = 'high' DESC,
                         a.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_role);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get announcement by ID
    public function getAnnouncementById($id) {
        $stmt = $this->conn->prepare("SELECT a.*, u.full_name as created_by_name 
                                      FROM announcements a 
                                      LEFT JOIN users u ON a.created_by = u.id 
                                      WHERE a.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get filtered announcements (for admin filtering)
    public function getFilteredAnnouncements($type = null, $target_role = null, $status = null) {
        $sql = "SELECT a.*, u.full_name as created_by_name,
                (SELECT COUNT(*) FROM announcement_views WHERE announcement_id = a.id) as view_count
                FROM announcements a 
                LEFT JOIN users u ON a.created_by = u.id 
                WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($type && $type != 'all') {
            $sql .= " AND a.announcement_type = ?";
            $params[] = $type;
            $types .= "s";
        }
        
        if ($target_role && $target_role != 'all') {
            $sql .= " AND a.target_role = ?";
            $params[] = $target_role;
            $types .= "s";
        }
        
        if ($status && $status != 'all') {
            $sql .= " AND a.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        $sql .= " ORDER BY a.priority = 'urgent' DESC, a.priority = 'high' DESC, a.created_at DESC";
        
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
    
    // Add new announcement
    public function addAnnouncement($title, $content, $announcement_type, $target_role, $priority, $status, $created_by, $expires_at = null) {
        // If expires_at is empty, set to NULL
        if (empty($expires_at)) {
            $expires_at = null;
        }
        
        $stmt = $this->conn->prepare("INSERT INTO announcements (title, content, announcement_type, target_role, priority, status, created_by, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssis", $title, $content, $announcement_type, $target_role, $priority, $status, $created_by, $expires_at);
        return $stmt->execute();
    }
    
    // Update announcement
    public function updateAnnouncement($id, $title, $content, $announcement_type, $target_role, $priority, $status, $expires_at = null) {
        if (empty($expires_at)) {
            $expires_at = null;
        }
        
        $stmt = $this->conn->prepare("UPDATE announcements SET title = ?, content = ?, announcement_type = ?, target_role = ?, priority = ?, status = ?, expires_at = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $title, $content, $announcement_type, $target_role, $priority, $status, $expires_at, $id);
        return $stmt->execute();
    }
    
    // Update announcement status only
    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE announcements SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    
    // Delete announcement
    public function deleteAnnouncement($id) {
        $stmt = $this->conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Mark announcement as viewed by user
    public function markAsViewed($announcement_id, $user_id) {
        // Check if already viewed
        $stmt = $this->conn->prepare("SELECT id FROM announcement_views WHERE announcement_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $announcement_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $stmt = $this->conn->prepare("INSERT INTO announcement_views (announcement_id, user_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $announcement_id, $user_id);
            return $stmt->execute();
        }
        return true;
    }
    
    // Check if user has viewed announcement
    public function hasViewed($announcement_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT id FROM announcement_views WHERE announcement_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $announcement_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    // Get announcement statistics
    public function getStats() {
        $stats = [];
        
        $result = $this->conn->query("SELECT COUNT(*) as total FROM announcements");
        $stats['total'] = $result->fetch_assoc()['total'];
        
        $result = $this->conn->query("SELECT COUNT(*) as active FROM announcements WHERE status = 'active'");
        $stats['active'] = $result->fetch_assoc()['active'];
        
        $result = $this->conn->query("SELECT COUNT(*) as inactive FROM announcements WHERE status = 'inactive'");
        $stats['inactive'] = $result->fetch_assoc()['inactive'];
        
        $result = $this->conn->query("SELECT COUNT(*) as expired FROM announcements WHERE status = 'expired' OR expires_at < CURDATE()");
        $stats['expired'] = $result->fetch_assoc()['expired'];
        
        $result = $this->conn->query("SELECT COUNT(*) as total_views FROM announcement_views");
        $stats['total_views'] = $result->fetch_assoc()['total_views'];
        
        return $stats;
    }
    
    // Get unread announcements count for user
    public function getUnreadCount($user_id, $user_role) {
        $sql = "SELECT COUNT(*) as unread FROM announcements a 
                WHERE a.status = 'active' 
                AND (a.expires_at IS NULL OR a.expires_at >= CURDATE())
                AND (a.target_role = 'all' OR a.target_role = ?)
                AND NOT EXISTS (SELECT 1 FROM announcement_views av WHERE av.announcement_id = a.id AND av.user_id = ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $user_role, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['unread'];
    }
    
    // Get announcement types list
    public function getAnnouncementTypes() {
        return ['maintenance', 'exam', 'update', 'general', 'emergency'];
    }
    
    // Get target roles list
    public function getTargetRoles() {
        return ['all', 'admin', 'instructor', 'teaching_assistant', 'student'];
    }
    
    // Get priorities list
    public function getPriorities() {
        return ['low', 'medium', 'high', 'urgent'];
    }
    
    // Search announcements
    public function searchAnnouncements($keyword) {
        $keyword = "%{$keyword}%";
        $stmt = $this->conn->prepare("SELECT a.*, u.full_name as created_by_name,
                                      (SELECT COUNT(*) FROM announcement_views WHERE announcement_id = a.id) as view_count
                                      FROM announcements a 
                                      LEFT JOIN users u ON a.created_by = u.id 
                                      WHERE a.title LIKE ? OR a.content LIKE ? 
                                      ORDER BY a.created_at DESC");
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get announcements by type
    public function getAnnouncementsByType($type) {
        $stmt = $this->conn->prepare("SELECT a.*, u.full_name as created_by_name 
                                      FROM announcements a 
                                      LEFT JOIN users u ON a.created_by = u.id 
                                      WHERE a.announcement_type = ? 
                                      ORDER BY a.created_at DESC");
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get announcements by target role
    public function getAnnouncementsByTargetRole($target_role) {
        $stmt = $this->conn->prepare("SELECT a.*, u.full_name as created_by_name 
                                      FROM announcements a 
                                      LEFT JOIN users u ON a.created_by = u.id 
                                      WHERE a.target_role = ? OR a.target_role = 'all'
                                      ORDER BY a.created_at DESC");
        $stmt->bind_param("s", $target_role);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get recent announcements (for dashboard)
    public function getRecentAnnouncements($limit = 5) {
        $user_role = $_SESSION['role'] ?? 'student';
        $sql = "SELECT a.*, u.full_name as created_by_name,
                (SELECT COUNT(*) FROM announcement_views WHERE announcement_id = a.id) as view_count
                FROM announcements a 
                LEFT JOIN users u ON a.created_by = u.id 
                WHERE a.status = 'active' 
                AND (a.expires_at IS NULL OR a.expires_at >= CURDATE())
                AND (a.target_role = 'all' OR a.target_role = ?)
                ORDER BY a.created_at DESC 
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $user_role, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>