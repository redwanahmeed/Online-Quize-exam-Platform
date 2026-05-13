<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/StudentModel.php';

class UserController {
    private $userModel;
    private $studentModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
    }
    
    public function index() {
        requireRole('admin');
        $users = $this->userModel->getAllUsers();
        include __DIR__ . '/../views/users/index.php';
    }
    
    public function create() {
        requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            $role = sanitize($_POST['role']);
            $full_name = sanitize($_POST['full_name']);
            
            if (usernameExists($username)) {
                header('Location: index.php?page=users&error=Username already exists');
                exit();
            }
            
            if (emailExists($email)) {
                header('Location: index.php?page=users&error=Email already exists');
                exit();
            }
            
            $user_id = $this->userModel->createUser($username, $email, $password, $role, $full_name);
            
            if ($user_id) {
                if ($role == 'student') {
                    $student_id = 'STU' . date('Y') . str_pad($user_id, 5, '0', STR_PAD_LEFT);
                    $this->studentModel->addStudent($student_id, $full_name, $email, '', '', '', 1, $user_id);
                }
                header('Location: index.php?page=users&success=User created successfully');
                exit();
            } else {
                header('Location: index.php?page=users&error=Failed to create user');
                exit();
            }
        }
    }
    
    public function delete() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $current_user_id = $_SESSION['user_id'];
        
        if ($id == $current_user_id) {
            header('Location: index.php?page=users&error=You cannot delete your own account');
            exit();
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            header('Location: index.php?page=users&error=User not found');
            exit();
        }
        
        if ($user['role'] == 'admin') {
            $adminCount = $this->userModel->countAdmins();
            if ($adminCount <= 1) {
                header('Location: index.php?page=users&error=Cannot delete the last admin user');
                exit();
            }
        }
        
        if ($user['role'] == 'student') {
            $this->studentModel->deleteStudentByUserId($id);
        }
        
        if ($this->userModel->deleteUser($id)) {
            header('Location: index.php?page=users&success=User deleted successfully');
            exit();
        } else {
            header('Location: index.php?page=users&error=Failed to delete user');
            exit();
        }
    }
    
    // Update Role via POST (from modal form)
    public function updateRole() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = (int)$_POST['user_id'];
            $new_role = sanitize($_POST['role']);
            
            // Validation
            if ($user_id <= 0) {
                header('Location: index.php?page=users&error=Invalid user ID');
                exit();
            }
            
            $valid_roles = ['admin', 'instructor', 'teaching_assistant', 'student'];
            if (!in_array($new_role, $valid_roles)) {
                header('Location: index.php?page=users&error=Invalid role');
                exit();
            }
            
            // Cannot change own role
            if ($user_id == $_SESSION['user_id']) {
                header('Location: index.php?page=users&error=You cannot change your own role');
                exit();
            }
            
            // Get user info
            $user = $this->userModel->getUserById($user_id);
            if (!$user) {
                header('Location: index.php?page=users&error=User not found');
                exit();
            }
            
            $old_role = $user['role'];
            
            // Cannot change last admin
            if ($old_role == 'admin' && $new_role != 'admin') {
                $adminCount = $this->userModel->countAdmins();
                if ($adminCount <= 1) {
                    header('Location: index.php?page=users&error=Cannot change the last admin\'s role');
                    exit();
                }
            }
            
            // Handle student conversion
            if ($old_role == 'student' && $new_role != 'student') {
                $this->studentModel->deleteStudentByUserId($user_id);
            }
            
            if ($old_role != 'student' && $new_role == 'student') {
                $student_id = 'STU' . date('Y') . str_pad($user_id, 5, '0', STR_PAD_LEFT);
                $this->studentModel->addStudent($student_id, $user['full_name'], $user['email'], '', '', '', 1, $user_id);
            }
            
            // Update role
            if ($this->userModel->updateRole($user_id, $new_role)) {
                header('Location: index.php?page=users&success=Role updated successfully from ' . ucfirst($old_role) . ' to ' . ucfirst(str_replace('_', ' ', $new_role)));
                exit();
            } else {
                header('Location: index.php?page=users&error=Failed to update role');
                exit();
            }
        } else {
            header('Location: index.php?page=users');
            exit();
        }
    }
    
    public function search() {
        requireRole('admin');
        
        header('Content-Type: application/json');
        
        if (isset($_POST['search'])) {
            $keyword = sanitize($_POST['search']);
            $users = $this->userModel->searchUsers($keyword);
            echo json_encode(['status' => 'success', 'users' => $users]);
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'No search keyword']);
        exit();
    }
}
?>