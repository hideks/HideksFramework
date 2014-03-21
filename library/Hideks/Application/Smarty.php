<?php

namespace Hideks\Application;

class Smarty {
    
    private static $instance = null;
    
    private $smarty = null;
    
    public static function getInstance() {
        if( is_null(self::$instance) ){
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function __construct() {
        $this->smarty = new \Smarty();
    }
    
    public function setTemplateDir($template_dir) {
        $this->smarty->template_dir = $template_dir;
    }
    
    public function setConfigDir($config_dir) {
        $this->smarty->config_dir = $config_dir;
    }
    
    public function setCompileDir($compile_dir) {
        $this->smarty->compile_dir = $compile_dir;
    }
    
    public function setCacheDir($cache_dir) {
        $this->smarty->cache_dir = $cache_dir;
    }
    
    public function setCaching($mode) {
        $this->smarty->caching = $mode;
    }
    
    public function setCompileCheck($mode) {
        $this->smarty->compile_check = $mode;
    }
    
    public function setForceCompile($mode) {
        $this->smarty->force_compile = $mode;
    }
    
    public function display($template) {
        $this->smarty->display($template);
    }
    
    public function assign($key, $value) {
        $this->smarty->assign($key, $value);
    }
    
}