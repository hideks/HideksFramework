<?php

namespace Hideks;

class Auth implements Auth\AuthInterface {
    
    private static $instance = null;
    
    private $session = null;
    
    public function __construct() {
        $this->session = \Hideks\Session::getInstance();
    }
    
    public static function getInstance() {
        if( empty(self::$instance) ){
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    public function write(Auth\Adapter\AdapterAbstract $adapter) {
        $data = $adapter->autenticate();
        
        if ($data) {
            $this->session->auth = true;
            
            foreach($data as $key => $value){
                $this->session->$key = $value;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function isLogged() {
        if (isset($this->session->auth)) {
            return true;
        }
        
        return false;
    }

    public function logout() {
        $this->session->destroy();
    }
    
}