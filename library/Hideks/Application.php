<?php

namespace Hideks;

class Application {
    
    private $_environment;
    
    private $_main;
    
    private $_config = array();
    
    public function __construct() {
        set_exception_handler(array(
            new \Hideks\Debug, 'handler'
        ));
        
        $this->setConfig(Config::getInstance());
    }
    
    public function getConfig() {
        return $this->_config;
    }
    
    private function setConfig(array $config) {
        $this->_config = $config;
        
        if(!empty($config['system']['environment'])){
            $this->_environment = $config['system']['environment'];
        }
        
        if(!empty($config['php']['settings'])){
            $this->setPhpSettings($config['php']['settings']);
        }
        
        $this->setMain();
    }
    
    private function setPhpSettings($phpSettings) {
        error_reporting(E_ALL | E_STRICT);

        date_default_timezone_set($phpSettings['timezone']);

        ini_set('display_errors', ($this->_environment === 'production') ? 0 : 1);
    }
    
    private function setMain() {
        $class = 'Main';
        
        $path = APPLICATION_PATH.DS.$class.'.php';
        
        if( !file_exists($path) ){
            throw new \Exception("The file: application/$class.php not found!!");
        }
        
        if( !class_exists($class, false) ){
            require_once($path);
            
            if( !class_exists($class, false) ){
                throw new \Exception("The class: $class doesn't exists!!");
            }
        }
        
        $this->_main = new \Main($this);
        
        if( !$this->_main instanceof Application\Main\MainInterface ){
            throw new \Exception("The class: $class doesn't implement \Hideks\Application\Main\MainInterface!!");
        }
        
        return $this;
    }
    
    public function getMain() {
        return $this->_main;
    }
    
    public function main() {
        $this->getMain()->main();
        
        return $this;
    }
    
    public function run() {
        $this->getMain()->run();
    }
    
}