<?php
class AuthController extends Controller {
    
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=dashboard&action=index");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $user = $userModel->findByEmail($email);
            if (!$user) {
                $user = $userModel->findByUsername($email);
            }
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                
                header("Location: index.php?controller=dashboard&action=index");
                exit();
            } else {
                $error = "Invalid email/username or password";
                $this->view('auth/login', ['error' => $error]);
            }
        } else {
            $this->view('auth/login');
        }
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            
            $errors = [];
            
            if (empty($_POST['username'])) {
                $errors[] = "Username is required";
            } elseif ($userModel->findByUsername($_POST['username'])) {
                $errors[] = "Username already exists";
            }
            
            if (empty($_POST['email'])) {
                $errors[] = "Email is required";
            } elseif ($userModel->findByEmail($_POST['email'])) {
                $errors[] = "Email already exists";
            }
            
            if (empty($_POST['password'])) {
                $errors[] = "Password is required";
            } elseif (strlen($_POST['password']) < 4) {
                $errors[] = "Password must be at least 4 characters";
            }
            
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $errors[] = "Passwords do not match";
            }
            
            if (empty($errors)) {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'full_name' => $_POST['full_name'],
                    'role' => 'student',
                    'department' => $_POST['department'],
                    'bio' => $_POST['bio']
                ];
                
                if ($userModel->create($data)) {
                    $success = "Account created successfully! Please login.";
                    $this->view('auth/login', ['success' => $success]);
                } else {
                    $error = "Failed to create account";
                    $this->view('auth/register', ['error' => $error]);
                }
            } else {
                $this->view('auth/register', ['errors' => $errors]);
            }
        } else {
            $this->view('auth/register');
        }
    }
    
    public function logout() {
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
    
    // Profile method added here
    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        
        $userModel = $this->model('User');
        $user = $userModel->find($_SESSION['user_id']);
        $success = null;
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profile_pic = null;
            
            // Handle profile picture upload
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
                $targetDir = __DIR__ . '/../../public/uploads/';
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
                $targetFile = $targetDir . $fileName;
                
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
                    $profile_pic = $fileName;
                }
            }
            
            // Update user profile
            if ($profile_pic) {
                $stmt = $userModel->db->prepare("UPDATE users SET full_name = ?, department = ?, bio = ?, profile_pic = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $_POST['full_name'], $_POST['department'], $_POST['bio'], $profile_pic, $_SESSION['user_id']);
            } else {
                $stmt = $userModel->db->prepare("UPDATE users SET full_name = ?, department = ?, bio = ? WHERE id = ?");
                $stmt->bind_param("sssi", $_POST['full_name'], $_POST['department'], $_POST['bio'], $_SESSION['user_id']);
            }
            
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $_POST['full_name'];
                $success = "Profile updated successfully!";
                // Refresh user data
                $user = $userModel->find($_SESSION['user_id']);
            } else {
                $error = "Failed to update profile.";
            }
            $stmt->close();
        }
        
        $this->view('auth/profile', [
            'user' => $user,
            'success' => $success,
            'error' => $error
        ]);
    }
}
?>