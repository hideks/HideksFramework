<?php

namespace Hideks\Router;

class Route {
    
    private $_path;
    
    private $_target;

    private $_methods = array('GET', 'POST', 'PUT', 'DELETE');
    
    private $_params;
    
    public function __construct($options) {
        $this->setPath( isset($options['path']) ? $options['path'] : null );
        
        $this->setTarget( isset($options['target']) ? $options['target'] : null );
        
        $this->setMethods( isset($options['methods']) ? explode(',', $options['methods']) : array() );
        
        $this->setParams( isset($options['params']) ? $options['params'] : array() );
    }
    
    public function getPath() {
        return $this->_path;
    }
    
    public function setPath($path) {
        $path = (string) $path;
        
        if(substr($path,-1) !== '/'){
            $path .= '/';
        }
        
        $this->_path = $path;
    }
    
    public function getTarget() {
        return $this->_target;
    }
    
    public function setTarget($target) {
        $this->_target = $target;
    }
    
    public function getMethods() {
        return $this->_methods;
    }
    
    public function setMethods(array $methods) {
        $this->_methods = $methods;
    }
    
    public function getParams() {
        return $this->_params;
    }
    
    public function setParams($params) {
        $this->_params = $params;
    }
    
    public function getRegex() {
       return preg_replace_callback("/{(\w+)}/", array(&$this, 'substituteParam'), $this->_path);
    }

    private function substituteParam($matches) {
        if (isset($matches[1]) && isset($this->_params[$matches[1]])) {
            return $this->_params[$matches[1]];
        }

        return "([\w-]+)";
    }
    
}