<?php

namespace Hideks\Auth;

class AuthInterface {
    
    public function write(Adapter\DatabaseAbstract $adapter);
    
    public function isLogged();
    
    public function logout();
    
}
