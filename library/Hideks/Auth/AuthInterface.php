<?php

namespace Hideks\Auth;

interface AuthInterface {
    
    public function write(Adapter\AdapterAbstract $adapter);
    
    public function isLogged();
    
    public function logout();
    
}
