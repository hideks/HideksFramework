<?php

namespace Hideks\Application;

class FrontController {
    
    private static $instance = null;
    
    private $_front = null;
    
    public static function getInstance() {
        if( is_null(self::$instance) ){
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function __construct() {
        $this->_front = new \Hideks\Controller\Front();
        
        $config = \Hideks\Config::getInstance();
        
        $this->_front->setRouter(\Hideks\Router::parseFile());
        
        $request = \Hideks\Request::getInstance($this->_front->getRouter());
        
        $this->_front->setRequest($request);
        
        if( $config['system']['environment'] === 'production' ){
            $config['smarty']['compile_check'] = 0;
            $config['smarty']['force_compile'] = 0;
        }
        
        if( $config['system']['environment'] === 'development' ){
            $config['smarty']['compile_check'] = 1;
            $config['smarty']['force_compile'] = 1;
        }

        if( $config['smarty']['force_compile'] ){
            $config['smarty']['caching'] = 0;
        }
        
        $smarty = new \Smarty();
        $smarty->template_dir    = APPLICATION_PATH.DS.'views'.DS;
        $smarty->config_dir      = APPLICATION_PATH.DS.'configs'.DS;
        $smarty->compile_dir     = APPLICATION_PATH.DS.'temp'.DS.'compiled'.DS;
        $smarty->cache_dir       = APPLICATION_PATH.DS.'temp'.DS.'cached'.DS;
        $smarty->caching         = $config['smarty']['caching'];
        $smarty->compile_check   = $config['smarty']['compile_check'];
        $smarty->force_compile   = $config['smarty']['force_compile'];
        
        $this->_front->setSmarty($smarty);
    }
    
    public function setController($controller) {
        $this->_front->getRequest()->setController($controller);
        
        return $this;
    }
    
    public function setAction($action) {
        $this->_front->getRequest()->setAction($action);
        
        return $this;
    }
    
    public function setParams(array $params) {
        $this->_front->getRequest()->setParams($params);
        
        return $this;
    }
    
    public function getRouter() {
        return $this->_front->getRouter();
    }
    
    public function getRequest() {
        return $this->_front->getRequest();
    }
    
    public function getSmarty() {
        return $this->_front->getSmarty();
    }
    
    public function dispatch() {
        $this->_front->dispatch();
    }
    
}