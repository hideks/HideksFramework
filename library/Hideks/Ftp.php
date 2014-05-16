<?php

namespace Hideks;

class Ftp {
    
    private $connection = null;
    
    public function __construct(array $params, $port = 21, $timeout = 90) {
        if( ( $this->connection = ftp_connect($params['hostname'], $port, $timeout) ) === false ){
            throw new \Exception("Unable to connect to ftp server on ".$params['hostname']);
        }
        
        if( ftp_login($this->connection, $params['username'], $params['password']) === false ){
            throw new \Exception("the user name or password is incorrect on ".$params['hostname']);
        }
    }
    
    public function setDirectory($directory) {
        ftp_chdir($this->connection, $directory);
    }
    
    public function getFile($local_file, $remote_file, $mode) {
        return ftp_get($this->connection, $local_file, $remote_file, $mode);
    }
    
    public function setFile($remote_file, $local_file, $mode) {
        ftp_put($this->connection, $remote_file, $local_file, $mode);
    }

    public function listDirectory($directory) {
        return ftp_nlist($this->connection, $directory);
    }
    
    public function delete($path) {
        ftp_delete($this->connection, $path);
    }
    
    public function close() {
        ftp_close($this->connection);
    }

}