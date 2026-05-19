<?php
class Controller {
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View not found: " . $view;
        }
    }
    
    protected function model($model) {
        $modelFile = __DIR__ . '/../app/models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        return null;
    }
}
?>