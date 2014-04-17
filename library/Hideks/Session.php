<?php

namespace Hideks;

class Session {
    
    private static $instance = null;
    
    private $session_state = false;

    public static function getInstance() {
        if( empty(self::$instance) ){
            self::$instance = new self();
        }
        
        self::$instance->name();
        
        self::$instance->start();
        
        return self::$instance;
    }
    
    public function name() {
        $config = \Hideks\Config::getInstance();

        if( isset($config['system']['name']) ){
            $matches = array();
        
            preg_match_all('/[A-Z]/', strtoupper($config['system']['name']), $matches);

            session_name( implode('', $matches[0]) );
        }
    }
    
    public function start() {
        if( $this->session_state === false ){
            $this->session_state = session_start();
        }
        
        return $this->session_state;
    }
    
    public function destroy() {
        if( $this->session_state === true ){
            $this->session_state = !session_destroy();
            
            return !$this->session_state;
        }
        
        return false;
    }
    
    public function __set($name, $value) {
        $_SESSION['Hideks_Session'][$name] = $value;
    }
    
    public function __get($name) {
        if( isset($_SESSION['Hideks_Session'][$name]) ){
            return $_SESSION['Hideks_Session'][$name];
        }
    }
    
    public function __isset($name) {
        return isset($_SESSION['Hideks_Session'][$name]);
    }
    
    public function __unset($name) {
        unset($_SESSION['Hideks_Session'][$name]);
    }
    
}