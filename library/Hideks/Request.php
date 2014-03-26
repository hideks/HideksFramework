<?php

namespace Hideks;

class Request {
    
    private static $instance = null;
    
    private $controller;
    
    private $action;
    
    private $params;
    
    public static function getInstance() {
        if( is_null(self::$instance) ){
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function getController() {
        return $this->controller;
    }
    
    public function getAction() {
        return $this->action;
    }

    public function _getParams($param) {
        return $this->params[$param];
    }

    public function dispatch(Router $router) {
        $requestUrl = $_SERVER['REQUEST_URI'];
        
        $route = $router->matchCurrentRequest();
        
        if($route){
            $path = $route->getTarget().$route->getParams();
        } else {
            $path = (isset($requestUrl) && $requestUrl !== '/') ? $requestUrl : 'index/index';
        }
        
        $explode = explode('/', $path);
        
        foreach($explode as $key => $value){
            if( empty($value) ){
                unset($explode[$key]);
            }
        }
        
        $parts = array();
        
        foreach($explode as $part){
            $parts[] = $part;
        }
        
        $this->controller = ucfirst($parts[0]);
        
        $action = ( !isset($parts[1]) || is_null($parts[1]) || $parts[1] === 'index' ) ? 'indexAction' : $parts[1];
        $action = ($action === 'indexAction') ? $action : $action.'Action';
        
        $this->action = $action;
        
        unset($parts[0], $parts[1]);
        
        if( is_null( end($parts) ) ){
            array_pop($parts);
        }
        
        if( !empty($parts) ){
            $index = 0;
            $indexes = array();
            $values = array();
            
            foreach($parts as $value){
                if($index % 2 == 0){
                    $indexes[] = $value;
                } else {
                    $values[] = $value;
                }
                
                $index++;
            }
        } else {
            $indexes = array();
            $values = array();
        }
        
        if(count($indexes) == count($values) && !empty($indexes) && !empty($values)){
            $this->params = array_combine($indexes, $values);
        } else {
            $this->params = array();
        }
        
        return $this;
    }
    
}