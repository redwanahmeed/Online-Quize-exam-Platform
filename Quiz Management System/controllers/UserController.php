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
        
        // Get filter parameters
        $role = $_GET['role'] ?? 'all';
        $status = $_GET['status'] ?? 'all';
        
        // Get filtered users
        $users = $this->userModel->getFilteredUsers($role, $status);
        
        include __DIR__ . '/../views/users/index.php';
    }
    
    public function view() {
        requireRole('admin');
        
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?page=users&error=Invalid user ID');
            exit();
        }
        
        $id = (int)$_GET['id'];
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            header('Location: index.php?page=users&error=User not found');
            exit();
        }
        
        $student = null;
        if ($user['role'] == 'student') {
            $student = $this->studentModel->getStudentByUserId($id);
        }
        
        include __DIR__ . '/../views/users/view.php';
    }
    
    public function create() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            $role = sanitize($_POST['role']);
            $full_name = sanitize($_POST['full_name']);
            
            // Validate password length
            if (strlen($password) < 6) {
                header('Location: index.php?page=users&error=Password must be at least 6 characters');
                exit();
            }
            
            // Check if username exists
            if (usernameExists($username)) {
                header('Location: index.php?page=users&error=Username already exists');
                exit();
            }
            
            // Check if email exists
            if (emailExists($email)) {
                header('Location: index.php?page=users&error=Email already exists');
                exit();
            }
            
            $user_id = $this->userModel->createUser($username, $email, $password, $role, $full_name);
            
            if ($user_id) {
                // If role is student, also create student record
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
    
    public function edit() {
        requireRole('admin');
        
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?page=users&error=Invalid user ID');
            exit();
        }
        
        $id = (int)$_GET['id'];
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            header('Location: index.php?page=users&error=User not found');
            exit();
        }
        
        include __DIR__ . '/../views/users/edit.php';
    }
    
    public function update() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = (int)$_POST['user_id'];
            $full_name = sanitize($_POST['full_name']);
            $email = sanitize($_POST['email']);
            $phone = sanitize($_POST['phone']);
            $address = sanitize($_POST['address']);
            
            // Check if email exists for other users
            if (emailExistsExcept($email, $id)) {
                header('Location: index.php?page=users&action=edit&id=' . $id . '&error=Email already exists');
                exit();
            }
            
            $conn = getDB();
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $id);
            
            if ($stmt->execute()) {
                // If user is student, update student table too
                $user = $this->userModel->getUserById($id);
                if ($user && $user['role'] == 'student') {
                    $student = $this->studentModel->getStudentByUserId($id);
                    if ($student) {
                        $this->studentModel->updateStudent($student['id'], $full_name, $email, $phone, $address, $student['course'], $student['year_level'], $student['status']);
                    }
                }
                header('Location: index.php?page=users&action=view&id=' . $id . '&success=User updated successfully');
                exit();
            } else {
                header('Location: index.php?page=users&action=edit&id=' . $id . '&error=Failed to update user');
                exit();
            }
        }
    }
    
    public function delete() {
        requireRole('admin');
        
        $id = (int)$_GET['id'];
        $current_user_id = $_SESSION['user_id'];
        
        // Cannot delete own account
        if ($id == $current_user_id) {
            header('Location: index.php?page=users&error=You cannot delete your own account');
            exit();
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            header('Location: index.php?page=users&error=User not found');
            exit();
        }
        
        // Cannot delete last admin
        if ($user['role'] == 'admin') {
            $adminCount = $this->userModel->countAdmins();
            if ($adminCount <= 1) {
                header('Location: index.php?page=users&error=Cannot delete the last admin user');
                exit();
            }
        }
        
        // If user is student, delete from students table first
        if ($user['role'] == 'student') {
            $this->studentModel->deleteStudentByUserId($id);
        }
        
        // Delete the user
        if ($this->userModel->deleteUser($id)) {
            header('Location: index.php?page=users&success=User deleted successfully');
            exit();
        } else {
            header('Location: index.php?page=users&error=Failed to delete user');
            exit();
        }
    }
    
    // Toggle user status (Activate/Deactivate)
    public function toggleStatus() {
        requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            
            $user_id = (int)$_POST['user_id'];
            $current_status = $_POST['current_status'];
            $new_status = ($current_status == 'active') ? 'inactive' : 'active';
            
            // Cannot change own status
            if ($user_id == $_SESSION['user_id']) {
                echo json_encode(['status' => 'error', 'message' => 'You cannot change your own status']);
                exit();
            }
            
            // Get user info
            $user = $this->userModel->getUserById($user_id);
            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                exit();
            }
            
            // Cannot deactivate last admin
            if ($user['role'] == 'admin' && $new_status == 'inactive') {
                $adminCount = $this->userModel->countAdmins();
                if ($adminCount <= 1) {
                    echo json_encode(['status' => 'error', 'message' => 'Cannot deactivate the last admin user']);
                    exit();
                }
            }
            
            // Update user status in users table
            if ($this->userModel->updateUserStatus($user_id, $new_status)) {
                // If user is student, also update student table status
                if ($user['role'] == 'student') {
                    $student = $this->studentModel->getStudentByUserId($user_id);
                    if ($student) {
                        $this->studentModel->updateStudentStatus($student['id'], $new_status);
                    }
                }
                echo json_encode(['status' => 'success', 'message' => 'User ' . $new_status . 'd successfully']);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
                exit();
            }
        }
    }
    
    // Update user role
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
            
            // Cannot change last admin's role
            if ($old_role == 'admin' && $new_role != 'admin') {
                $adminCount = $this->userModel->countAdmins();
                if ($adminCount <= 1) {
                    header('Location: index.php?page=users&error=Cannot change the last admin\'s role');
                    exit();
                }
            }
            
            // Handle student to other role conversion
            if ($old_role == 'student' && $new_role != 'student') {
                $this->studentModel->deleteStudentByUserId($user_id);
            }
            
            // Handle other role to student conversion
            if ($old_role != 'student' && $new_role == 'student') {
                $student_id = 'STU' . date('Y') . str_pad($user_id, 5, '0', STR_PAD_LEFT);
                $this->studentModel->addStudent($student_id, $user['full_name'], $user['email'], '', '', '', 1, $user_id);
            }
            
            // Update the role
            if ($this->userModel->updateRole($user_id, $new_role)) {
                header('Location: index.php?page=users&success=Role updated from ' . ucfirst($old_role) . ' to ' . ucfirst(str_replace('_', ' ', $new_role)));
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
    
    // Search users
    public function search() {
        requireRole('admin');
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
            $keyword = sanitize($_POST['search']);
            
            if (strlen($keyword) < 2) {
                echo json_encode(['status' => 'error', 'message' => 'Search keyword must be at least 2 characters']);
                exit();
            }
            
            $users = $this->userModel->searchUsers($keyword);
            echo json_encode(['status' => 'success', 'users' => $users]);
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'No search keyword provided']);
        exit();
    }
}
?>