<?php

namespace Hideks\Auth\Adapter;

class Database extends AdapterAbstract {
    
    private $connection = null;
    
    private $userColumn = null;
    
    private $passColumn = null;
    
    private $table = null;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    public function setUserColumn($userColumn) {
        $this->userColumn = $userColumn;
        
        return $this;
    }

    public function setPassColumn($passColumn) {
        $this->passColumn = $passColumn;
        
        return $this;
    }

    public function setTable($table) {
        $this->table = $table;
        
        return $this;
    }

    public function autenticate() {
        $stm = $this->connection->query("
            SELECT      *
            FROM        {$this->table}
            WHERE       {$this->userColumn} = '{$this->username}'
            AND         {$this->passColumn} = '{$this->password}'
        ");
        
        return $stm->fetch();
    }
    
}