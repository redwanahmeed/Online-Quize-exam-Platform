<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

spl_autoload_register(function($class) {
    $paths = [
        __DIR__ . '/../core/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/'
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

$controllers = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'course' => 'CourseController',
    'enrollment' => 'EnrollmentController',
    'quiz' => 'QuizController',
    'ta' => 'TaController'  // Make sure this line exists
];

if (isset($controllers[$controller])) {
    $controllerClass = $controllers[$controller];
    $controllerFile = __DIR__ . '/../app/controllers/' . $controllerClass . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $obj = new $controllerClass();
        if (method_exists($obj, $action)) {
            $obj->$action();
        } else {
            echo "Method not found: " . $action;
        }
    } else {
        echo "Controller file not found: " . $controllerFile;
    }
} else {
    echo "Controller not found: " . $controller;
}
?>