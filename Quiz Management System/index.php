<?php
require_once __DIR__ . '/config/config.php';

// Routing
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';


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
            if ($action == 'approveRequest') {
                $controller->approveRequest();
            } elseif ($action == 'rejectRequest') {
                $controller->rejectRequest();
            } else {
                $controller->index();
            }
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
    } elseif ($action == 'view') {
        $controller->view();
    } elseif ($action == 'search') {
        $controller->search();
    } elseif ($action == 'updateStatus') {
        $controller->updateStatus();
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
    } elseif ($action == 'view') {
        $controller->view();
    } elseif ($action == 'edit') {
        $controller->edit();
    } elseif ($action == 'update') {
        $controller->update();
    } elseif ($action == 'toggleStatus') {
        $controller->toggleStatus();
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


        case 'quizzes':
    require_once __DIR__ . '/controllers/QuizController.php';
    $controller = new QuizController();
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
    } elseif ($action == 'updateStatus') {
        $controller->updateStatus();
    } else {
        $controller->index();
    }
    break;

    case 'announcements':
    require_once __DIR__ . '/controllers/AnnouncementController.php';
    $controller = new AnnouncementController();
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
    } elseif ($action == 'updateStatus') {
        $controller->updateStatus();
    } else {
        $controller->index();
    }
    break;


    case 'reports':
    require_once __DIR__ . '/controllers/ReportController.php';
    $controller = new ReportController();
    if ($action == 'search') {
        $controller->search();
    } elseif ($action == 'generatePDF') {
        $controller->generatePDF();
    } else {
        $controller->index();
    }
    break;


    case 'audit':
    require_once __DIR__ . '/controllers/AuditLogController.php';
    $controller = new AuditLogController();
    if ($action == 'view') {
        $controller->view();
    } elseif ($action == 'clear') {
        $controller->clear();
    } elseif ($action == 'getRecent') {
        $controller->getRecent();
    } else {
        $controller->index();
    }
    break;
    
        
    default:
        header('Location: index.php?page=login');
        break;
}
?>