<?php

namespace Hideks\Auth\Adapter;

class Database extends DatabaseAbstract {
    
    private $connection = null;
    
    private $userColumn = null;
    
    private $passColumn = null;
    
    private $table = null;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    public function setUserColumn($userColumn) {
        $this->userColumn = $userColumn;
    }

    public function setPassColumn($passColumn) {
        $this->passColumn = $passColumn;
    }

    public function setTable($table) {
        $this->table = $table;
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