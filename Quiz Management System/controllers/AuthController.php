<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = sanitize($_POST['username']);
            $password = $_POST['password'];
            
            $user = $this->userModel->authenticate($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                
                header('Location: index.php?page=dashboard');
                exit();
            } else {
                $error = "Invalid username/email or password!";
                include __DIR__ . '/../views/auth/login.php';
            }
        } else {
            include __DIR__ . '/../views/auth/login.php';
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: index.php?page=login');
        exit();
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            $full_name = sanitize($_POST['full_name']);
            $role = 'student';
            
            if ($this->userModel->createUser($username, $email, $password, $role, $full_name)) {
                header('Location: index.php?page=login&registered=1');
                exit();
            } else {
                $error = "Registration failed. Username or email may already exist.";
                include __DIR__ . '/../views/auth/register.php';
            }
        } else {
            include __DIR__ . '/../views/auth/register.php';
        }
    }
}
?>