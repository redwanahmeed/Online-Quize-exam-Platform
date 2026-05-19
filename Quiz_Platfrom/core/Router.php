<?php
class Router {
    private $controller = 'AuthController';
    private $action = 'login';
    
    public function dispatch() {
        if (isset($_GET['controller']) && isset($_GET['action'])) {
            $this->controller = ucfirst($_GET['controller']) . 'Controller';
            $this->action = $_GET['action'];
        }
        
        $controllerFile = "../app/controllers/{$this->controller}.php";
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $this->controller();
            
            if (method_exists($controller, $this->action)) {
                call_user_func([$controller, $this->action]);
            } else {
                die("Action {$this->action} not found in controller {$this->controller}");
            }
        } else {
            die("Controller {$this->controller} not found");
        }
    }
}
?>