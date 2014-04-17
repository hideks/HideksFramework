<?php

namespace Hideks\Auth\Adapter;

interface AdapterInterface {
    
    public function setUsername($username);
    
    public function setPassword($password);
    
    public function autenticate();
    
}