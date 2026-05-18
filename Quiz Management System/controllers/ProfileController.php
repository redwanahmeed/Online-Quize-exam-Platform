<?php
require_once __DIR__ . '/../models/UserModel.php';

class ProfileController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function index() {
        requireAuth();
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        include __DIR__ . '/../views/profile/index.php';
    }
    
    public function update() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = sanitize($_POST['full_name']);
            $email = sanitize($_POST['email']);
            $phone = sanitize($_POST['phone']);
            $address = sanitize($_POST['address']);
            
            // Handle profile image upload
            $profile_image = $_SESSION['profile_image'] ?? '';
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['profile_image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    $upload_path = __DIR__ . '/../assets/uploads/';
                    
                    // Create directory if not exists
                    if (!file_exists($upload_path)) {
                        mkdir($upload_path, 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path . $new_filename)) {
                        $profile_image = $new_filename;
                    }
                }
            }
            
            if ($this->userModel->updateProfile($_SESSION['user_id'], $full_name, $email, $phone, $address, $profile_image)) {
                // Update session data
                $_SESSION['full_name'] = $full_name;
                $_SESSION['profile_image'] = $profile_image;
                
                header('Location: index.php?page=profile&success=Profile updated successfully');
                exit();
            } else {
                header('Location: index.php?page=profile&error=Failed to update profile');
                exit();
            }
        }
    }
    
    public function changePassword() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password !== $confirm_password) {
                header('Location: index.php?page=profile&error=New passwords do not match');
                exit();
            }
            
            if (strlen($new_password) < 6) {
                header('Location: index.php?page=profile&error=Password must be at least 6 characters');
                exit();
            }
            
            if ($this->userModel->changePassword($_SESSION['user_id'], $current_password, $new_password)) {
                header('Location: index.php?page=profile&success=Password changed successfully');
                exit();
            } else {
                header('Location: index.php?page=profile&error=Current password is incorrect');
                exit();
            }
        }
    }
}
?>