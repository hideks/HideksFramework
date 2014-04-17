<?php

namespace Hideks;

class Config {
    
    private static $instance = null;
    
    private $file = null;
    
    private $config = array();

    public static function getInstance() {
        if( !isset(self::$instance) ){
            $config = new self();
            
            self::$instance = $config->find()->parse()->getEnvConfig();
        }
        
        return self::$instance;
    }
    
    public function find() {
        $dir = APPLICATION_PATH.DS.'configs'.DS;
        
        if ( ( $handle = opendir($dir) ) ) {
            $files = array();
            
            while (false !== ($file = readdir($handle))) {
                if(preg_match('/^configuration.(.+)/', $file)){
                    $files[] = $file;
                }
            }

            closedir($handle);
        } else {
            throw new \Exception("Permission denied in: {$dir}");
        }
        
        $count = count($files);
        
        if( $count === 0 ){
            throw new \Exception("Configuration file not found in: {$dir}");
        }

        if( $count > 1){
            throw new \Exception("You should have one configuration file in: {$dir}");
        }

        $this->file = $dir . $files[0];
        
        return $this;
    }
    
    public function parse() {
        $file_ext = pathinfo($this->file, PATHINFO_EXTENSION);
        
        switch($file_ext){
            case 'ini':
                $config = new Config\Ini($this->file);
                break;
            default:
                throw new \Exception("The file extension: {$file_ext} is not supported in: {$this->file}");
        }
        
        $this->config = $config->get();
        
        return $this;
    }
    
    public function getEnvConfig() {
        $environment = $this->config['general']['system']['environment'];
        
        if( $environment !== 'production' && $environment !== 'development'){
            throw new \Exception("The environment: {$environment} is not supported!!");
        }
        
        return $this->config[$environment];
    }
    
}