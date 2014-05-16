<?php

namespace Hideks;

class Request {
    
    private static $instance = null;
    
    private $controller;
    
    private $action;
    
    private $params;
    
    public static function getInstance(Router $router) {
        if( is_null(self::$instance) ){
            self::$instance = new self($router);
        }
        
        return self::$instance;
    }
    
    public function __construct(Router $router) {
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
        
        if( ( $parts[0] === 'images' || $parts[0] === 'javascripts' || $parts[0] === 'stylesheets' ) && !file_exists($path) ){
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            exit;
        }
        
        $this->controller = $parts[0];
        
        $action = ( !isset($parts[1]) || is_null($parts[1]) || $parts[1] === 'index' ) ? 'index' : $parts[1];
        
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
                if($index++ % 2 == 0){
                    $indexes[] = $value;
                } else {
                    $values[] = $value;
                }
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
    }
    
    public function getController() {
        return $this->controller;
    }
    
    public function setController($controller) {
        $this->controller = $controller;
    }
    
    public function getAction() {
        return $this->action;
    }
    
    public function setAction($action) {
        $this->action = $action;
    }

    public function getParams() {
        return $this->params;
    }
    
    public function getParam($param, $value = false) {
        if( isset($this->params[$param]) ){
            return $this->params[$param];
        }
        
        return $value;
    }
    
    public function setParams(array $params) {
        $this->params = $params;
    }

    public function redirectTo($url, array $params = array()) {
        $status = isset($params['status']) ? $params['status'] : null;
        
        if($status){
            unset($params['status']);
        }
        
        if( isset($status) ){
            header("location: {$url}", true, $status);
        } else {
            header("location: {$url}");
        }
    }
    
}