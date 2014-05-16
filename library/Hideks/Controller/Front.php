<?php

namespace Hideks\Controller;

class Front {
    
    private $_router;
    
    private $_request;
    
    private $_smarty;
    
    public function getRouter() {
        return $this->_router;
    }
    
    public function setRouter($router) {
        $this->_router = $router;
    }
    
    public function getRequest() {
        return $this->_request;
    }
    
    public function setRequest($request) {
        $this->_request = $request;
    }
    
    public function getSmarty() {
        return $this->_smarty;
    }
    
    public function setSmarty($smarty) {
        $this->_smarty = $smarty;
    }
    
    public function dispatch() {
        $controller = ucfirst($this->_request->getController());
        
        if( !file_exists(APPLICATION_PATH.DS.'controllers'.DS.$controller.'.php')){
            throw new \Exception($controller.' controller not found!!', 1);
        }
        
        require_once(APPLICATION_PATH.DS.'controllers'.DS.$controller.'.php');
        
        $controller = new $controller($this);
        
        $action = "{$this->_request->getAction()}Action";
        
        if( !method_exists($controller, $action) ){
            throw new \Exception($this->_request->getAction().' action not found!!', 1);
        }
        
        $controller->$action();
    }
    
}