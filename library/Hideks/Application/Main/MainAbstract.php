<?php

namespace Hideks\Application\Main;

abstract class MainAbstract implements MainInterface {
    
    private $_application;
    
    private $_smarty;

    public function __construct($application) {
        $this->setApplication($application);
    }
    
    public function getApplication() {
        return $this->_application;
    }
    
    public function setApplication($application) {
        $this->_application = $application;
    }
    
    public function getSmarty() {
        return $this->_smarty;
    }
    
    final public function main() {
        $config = $this->_application->getConfig();
        
        // Inicia a session
        session_name($config['system']['name']);
        session_start();
        
        // Inicia o smarty
        if( $config['system']['environment'] === 'production' ){
            $config['smarty']['compile_check'] = 0;
            $config['smarty']['force_compile'] = 0;
        }

        if( $config['smarty']['force_compile'] ){
            $config['smarty']['caching'] = 0;
        }
        
        $this->_smarty = \Hideks\Application\Smarty::getInstance();
        $this->_smarty->setTemplateDir(APPLICATION_PATH.DS.'views/');
        $this->_smarty->setConfigDir(APPLICATION_PATH.DS.'configs/');
        $this->_smarty->setCompileDir(APPLICATION_PATH.DS.'temp/compiled/');
        $this->_smarty->setCacheDir(APPLICATION_PATH.DS.'temp/cached/');
        $this->_smarty->setCaching($config['smarty']['caching']);
        $this->_smarty->setCompileCheck($config['smarty']['compile_check']);
        $this->_smarty->setForceCompile($config['smarty']['force_compile']);
        
        //Inicia o active record
        if( $config['system']['enabled']['component']['active_record'] ){
            \ActiveRecord\Config::initialize(function($cfg) use($config) {
                $cfg->set_model_directory(APPLICATION_PATH.DS.'models');
                $cfg->set_connections($config['system']['connection']);
                $cfg->set_default_connection($config['system']['environment']);
            });
        }
    }
    
}