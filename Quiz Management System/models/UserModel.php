<?php
require_once __DIR__ . '/../config/config.php';

class UserModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Authenticate user
    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, password, role, full_name, profile_image, status FROM users WHERE (username = ? OR email = ?) AND status = 'active'");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }
    
    // Get user by ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, username, email, phone, address, role, full_name, profile_image, status, created_at, last_login FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get user by username
    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id, username, email, role, full_name, status FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get user by email
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, username, email, role, full_name, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // Get all users
    public function getAllUsers() {
        $result = $this->conn->query("SELECT id, username, email, role, full_name, status, created_at FROM users ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Create new user
    public function createUser($username, $email, $password, $role, $full_name) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $status = 'active';
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role, full_name, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $hashed_password, $role, $full_name, $status);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    // Update user profile
    public function updateProfile($user_id, $full_name, $email, $phone, $address, $profile_image = null) {
        if ($profile_image) {
            $stmt = $this->conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, profile_image = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $full_name, $email, $phone, $address, $profile_image, $user_id);
        } else {
            $stmt = $this->conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);
        }
        return $stmt->execute();
    }
    
  // Update user status (activate/deactivate)
    public function updateUserStatus($user_id, $status) {
    $stmt = $this->conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $user_id);
    return $stmt->execute();
}
    
    // Activate user
    public function activateUser($user_id) {
        return $this->updateUserStatus($user_id, 'active');
    }
    
    // Deactivate user
    public function deactivateUser($user_id) {
        return $this->updateUserStatus($user_id, 'inactive');
    }
    
    // Update user role
    public function updateRole($user_id, $role) {
        $valid_roles = ['admin', 'instructor', 'teaching_assistant', 'student'];
        if (!in_array($role, $valid_roles)) {
            return false;
        }
        
        $stmt = $this->conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $user_id);
        return $stmt->execute();
    }
    
    // Change password
    public function changePassword($user_id, $current_password, $new_password) {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            return $stmt->execute();
        }
        return false;
    }
    
    // Delete user
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Count admin users
    public function countAdmins() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND status = 'active'");
        $data = $result->fetch_assoc();
        return $data['count'];
    }
    
    // Count users by role
    public function countUsersByRole($role) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['count'];
    }
    
    // Get total users count
    public function getTotalUsers() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM users");
        $data = $result->fetch_assoc();
        return $data['total'];
    }
    
    // Update last login
    public function updateLastLogin($user_id) {
        $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
    
    // Search users
    public function searchUsers($keyword) {
        $keyword = "%{$keyword}%";
        $stmt = $this->conn->prepare("SELECT id, username, email, role, full_name, status, created_at FROM users WHERE username LIKE ? OR full_name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
        $stmt->bind_param("sss", $keyword, $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get users by role
    public function getUsersByRole($role) {
        $stmt = $this->conn->prepare("SELECT id, username, email, full_name, status, created_at FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get filtered users
    public function getFilteredUsers($role = null, $status = null) {
        $sql = "SELECT id, username, email, role, full_name, status, created_at FROM users WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($role && $role != 'all') {
            $sql .= " AND role = ?";
            $params[] = $role;
            $types .= "s";
        }
        
        if ($status && $status != 'all') {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>