<?php
require_once __DIR__ . '/config/config.php';

// Routing
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

// Route to appropriate controller
switch($page) {
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;
        
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'students':
        require_once __DIR__ . '/controllers/StudentController.php';
        $controller = new StudentController();
        if ($action == 'create') {
            $controller->create();
        } elseif ($action == 'edit') {
            $controller->edit();
        } elseif ($action == 'delete') {
            $controller->delete();
        } elseif ($action == 'view') {
            $controller->view();
        } elseif ($action == 'search') {
            $controller->search();
        } else {
            $controller->index();
        }
        break;
        
    case 'courses':
        require_once __DIR__ . '/controllers/CourseController.php';
        $controller = new CourseController();
        if ($action == 'create') {
            $controller->create();
        } elseif ($action == 'edit') {
            $controller->edit();
        } elseif ($action == 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;
        
case 'users':
    require_once __DIR__ . '/controllers/UserController.php';
    $controller = new UserController();
    if ($action == 'create') {
        $controller->create();
    } elseif ($action == 'delete') {
        $controller->delete();
    } elseif ($action == 'updateRole') {
        $controller->updateRole();
    } elseif ($action == 'search') {
        $controller->search();
    } else {
        $controller->index();
    }
    break;
        
    case 'reports':
        require_once __DIR__ . '/models/StudentModel.php';
        include __DIR__ . '/views/reports/index.php';
        break;
        
    case 'profile':
        require_once __DIR__ . '/controllers/ProfileController.php';
        $controller = new ProfileController();
        if ($action == 'update') {
            $controller->update();
        } elseif ($action == 'change_password') {
            $controller->changePassword();
        } else {
            $controller->index();
        }
        break;
        
    default:
        header('Location: index.php?page=login');
        break;
}
?>