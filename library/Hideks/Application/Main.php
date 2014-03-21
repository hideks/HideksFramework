<?php

namespace Hideks\Application;

class Main extends Main\MainAbstract {
    
    private $_router;
    
    private $_request;
    
    public function __construct($application) {
        parent::__construct($application);
    }
    
    public function getRouter() {
        return $this->_router;
    }
    
    public function getRequest() {
        return $this->_request;
    }
    
    public function run() {
        $application = $this->getApplication();
        
        $config = $application->getConfig();
        
        $router = \Hideks\Router::parseConfig(APPLICATION_PATH.DS.'configs'.DS.'routes.ini');
        $router->setBasePath($config['system']['router']['basepath']);
        
        $this->_router = $router;
        
        $this->_request = \Hideks\Request::getInstance()->dispatch($router);
        
        // Inicia os métodos da classe Main
        $methods = get_class_methods(get_class($this));
        
        foreach($methods as $method){
            if( preg_match('/^init(\w+)$/', $method) ){
                $this->$method();
            }
        }
        
        // Inicia o controller da requisição
        $controller = $this->_request->getController();
        
        if( !file_exists(APPLICATION_PATH.DS.'controllers'.DS.$controller.'.php')){
            throw new \Exception($controller.' controller not found!!');
        }
        
        require_once(APPLICATION_PATH.DS.'controllers'.DS.$controller.'.php');
        
        $controller = new $controller($this);
        
        // Inicia a action da requisição
        $action = $this->_request->getAction();
        
        if( !method_exists($controller, $action) ){
            throw new \Exception($action.' action not found!!');
        }
        
        $controller->$action();
    }
    
}