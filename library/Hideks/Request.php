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
        $requestUrl = filter_input(INPUT_SERVER, 'REQUEST_URI');
        
        $route = $router->matchCurrentRequest();
        
        if($route){
            $path = $route->getTarget().$route->getParams();
        } else {
            $path = (isset($requestUrl)) ? $requestUrl : 'index/index';
        }
        
        $explode = explode('/', $path);
        
        foreach($explode as $key => $value){
            if( empty($value) ){
                unset($explode[$key]);
            }
        }
        
        sort($explode);
        
        $this->controller = ucfirst($explode[0]);
        
        $action = ( !isset($explode[1]) || is_null($explode[1]) || $explode[1] === 'index' ) ? 'indexAction' : $explode[1];
        $action = ($action === 'indexAction') ? $action : $action.'Action';
        
        $this->action = $action;
        
        unset($explode[0], $explode[1]);
        
        if( is_null( end($explode) ) ){
            array_pop($explode);
        }
        
        if( !empty($explode) ){
            $index = 0;
            $indexes = array();
            $values = array();
            
            foreach($explode as $value){
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