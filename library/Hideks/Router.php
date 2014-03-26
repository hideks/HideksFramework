<?php

namespace Hideks;

class Router {
    
    private $_routes = array();
    
    private $_basePath = '';
    
    public function __construct(Router\Collection $collection) {
        $this->_routes = $collection;
    }
    
    private function getBasePath() {
        return $this->_basePath;
    }
    
    public function setBasePath($basePath) {
        $this->_basePath = (string) $basePath;
    }
    
    public function matchCurrentRequest() {
        $requestMethod  = $_SERVER['REQUEST_METHOD'];
        
        $requestUrl     = $_SERVER['REQUEST_URI'];
        
        if( isset($_POST['_method']) && strtoupper($_POST['_method']) && in_array($_POST['_method'], array('PUT','DELETE')) ){
            $requestMethod = $_POST['_method'];
        } else {
            $requestMethod = $requestMethod;
        }
        
        if (($pos = strpos($requestUrl, '?')) !== false) {
            $requestUrl = substr($requestUrl, 0, $pos);
        }

        return $this->match($requestUrl, $requestMethod);
    }
    
    private function match($requestUrl, $requestMethod = 'GET') {
        foreach($this->_routes->all() as $routes){
            if (! in_array($requestMethod, (array) $routes->getMethods())) {
                continue;
            }
            
            $matches = array();
            
            if (! preg_match("@^".$this->getBasePath().$routes->getRegex()."*$@i", $requestUrl, $matches)) {
                continue;
            }
            
            $routes->setParams($this->matchArgumentKeys($routes->getPath(), $matches));

            return $routes;
        }
        
        return false;
    }
    
    private function matchArgumentKeys($path, $matches) {
        $keys = array();
        
        $params = null;
        
        if (preg_match_all("/{([\w-]+)}/", $path, $keys)) {
            $keys = $keys[1];

            foreach ($keys as $key => $name) {
                if (isset($matches[$key + 1])) {
                    $params .= DS.$name.DS.$matches[$key + 1];
                }
            }
        }
        
        return $params;
    }
    
    public function _linkTo($routeName, array $params = array()) {
        if( !$this->_routes->exists($routeName) ){
            throw new \Exception("No route with the name $routeName has been found.");
        }
        
        $route = $this->_routes->get($routeName);
        
        $url = $route->getPath();
        
        $param_keys = array();
        
        if( $params && preg_match_all("/{(\w+)}/", $url, $param_keys) ){
            $param_keys = $param_keys[1];

            foreach ($param_keys as $key) {
                if( isset($params[$key]) ){
                    $url = preg_replace("/{(\w+)}/", $params[$key], $url, 1);
                }
            }
        }
        
        return $url;
    }
    
    public static function parseConfig($file) {
        $config = new \Hideks\Config\Ini($file);
        
        $routes = $config->get();
        
        $collection = new Router\Collection();
        
        foreach($routes as $routeName => $options){
            $collection->add($routeName, new Router\Route($options));
        }
        
        return new Router($collection);
    }
    
}