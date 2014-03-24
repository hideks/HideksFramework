<?php

namespace Hideks\Auth\Adapter;

abstract class DatabaseAbstract {
    
    protected $username = null;
    
    protected $password = null;
    
    public function setUsername($username) {
        $this->username = $username;
        
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        
        return $this;
    }

    abstract public function autenticate();
}