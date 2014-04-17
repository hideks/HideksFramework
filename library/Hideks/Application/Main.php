<?php

namespace Hideks\Application;

class Main extends Main\MainAbstract {
    
    private $_router;
    
    private $_request;
    
    private $_smarty;

    public function __construct($application) {
        parent::__construct($application);
    }
    
    public function getRouter() {
        return $this->_router;
    }
    
    public function getRequest() {
        return $this->_request;
    }
    
    public function getSmarty() {
        return $this->_smarty;
    }
    
    public function run() {
        $front = new FrontController();
        
        $this->_router = $front->getRouter();
        
        $this->_request = $front->getRequest();
        
        $this->_smarty = $front->getSmarty();
        
        $methods = get_class_methods(get_class($this));
        
        foreach($methods as $method){
            if( preg_match('/^init(\w+)$/', $method ) ){
                $this->$method();
            }
        }
        
        $front->dispatch();
    }
    
}