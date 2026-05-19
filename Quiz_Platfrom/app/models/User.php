<?php
class User extends Model {
    public $db; // Make db public for access in controller
    
    public function __construct() {
        parent::__construct();
        $this->db = $this->db; // Set the db property
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, full_name, role, department, bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", 
            $data['username'], 
            $data['email'], 
            $data['password'], 
            $data['full_name'], 
            $data['role'], 
            $data['department'], 
            $data['bio']
        );
        return $stmt->execute();
    }
}
?>