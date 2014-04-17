<?php

namespace Hideks\Application\Main;

abstract class MainAbstract implements MainInterface {
    
    protected $_config;
    
    private $_smarty;

    public function __construct($application) {
        $this->_config = $application->getConfig();
    }
    
    public function getSmarty() {
        return $this->_smarty;
    }
    
    public function main() {
        $config = $this->_config;
        
        if( $this->_config['system']['enabled']['component']['active_record'] ){
            \ActiveRecord\Config::initialize(function($cfg) use($config) {
                $cfg->set_model_directory(APPLICATION_PATH.DS.'models');
                $cfg->set_connections($config['system']['connection']);
                $cfg->set_default_connection($config['system']['environment']);
            });
        }
    }
    
}