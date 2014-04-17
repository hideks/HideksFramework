<?php

namespace Hideks\Auth\Adapter;

abstract class AdapterAbstract implements AdapterInterface {
    
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

}