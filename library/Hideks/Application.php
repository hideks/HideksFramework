<?php

namespace Hideks;

class Application {
    
    private $_environment;
    
    private $_main;
    
    private $_config = array();
    
    public function __construct() {
        if ( version_compare(PHP_VERSION, '5.3.0', '<') ){
            throw new \Exception('Hideks Framework requires PHP 5.3 or higher');
        }
        
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
        if( $this->_environment === 'production' ){
            $phpSettings['error_reporting'] = 0;
            $phpSettings['display_errors']  = 0;
        }
        
        error_reporting($phpSettings['error_reporting']);

        date_default_timezone_set($phpSettings['timezone']);

        ini_set('display_errors', $phpSettings['display_errors']);
    }
    
    private function setMain() {
        $class = 'Main';
        
        $path = APPLICATION_PATH.DS.$class.'.php';
        
        if( !class_exists($class, false) ){
            require_once($path);
            
            if( !class_exists($class, false) ){
                throw new \Exception('Main class not found!!');
            }
        }
        
        $this->_main = new \Main($this);
        
        if( !$this->_main instanceof Application\Main\MainInterface ){
            throw new \Exception('Main class does not implement \Hideks\Application\Main\MainInterface!!');
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