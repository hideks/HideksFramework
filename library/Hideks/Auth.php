<?php

namespace Hideks;

class Auth {
    
    private static $instance = null;
    
    public static function getInstance() {
        if( is_null(self::$instance) ){
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function write(Auth\Adapter\DatabaseAbstract $adapter) {
        $data = $adapter->autenticate();
        
        if ($data) {
            $_SESSION['HF_Session']['auth'] = true;
            
            foreach($data as $key => $value){
                $_SESSION['HF_Session']['user'][$key] = $value;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function isLogged() {
        if (isset($_SESSION['HF_Session']['auth'])) {
            return true;
        }
        
        return false;
    }

    public function logout() {
        session_destroy();
    }
    
}