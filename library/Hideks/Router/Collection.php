<?php

namespace Hideks\Router;

class Collection {
    
    private $_routes = array();
    
    public function add($routeName, Route $options) {
        $this->_routes[$routeName] = $options;
    }
    
    public function all() {
        return $this->_routes;
    }
    
    public function get($routeName) {
        return $this->_routes[$routeName];
    }
    
    public function exists($routeName) {
        if( isset($this->_routes[$routeName]) ){
            return true;
        }
        
        return false;
    }
    
}