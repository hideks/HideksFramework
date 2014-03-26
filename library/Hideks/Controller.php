<?php

namespace Hideks;

class Controller {
    
    private $_router;
    
    private $_request;
    
    private $_smarty;

    public function __construct($frontController) {
        $this->_router = $frontController->getRouter();
        
        $this->_request = $frontController->getRequest();
        
        $this->_smarty = $frontController->getSmarty();
    }
    
    public function renderTo($output, $options = null) {
        if( !method_exists($this, $output) ){
            throw new \Exception('The output '.$output.' is not supported!!');
        }
        
        call_user_func_array(array($this, $output), array($options));
    }
    
    private function html($options) {
        header('Content-Type: text/html; charset=utf-8');

        $controller = strtolower( $this->_request->getController() );

        $action = str_replace("Action", "", $this->_request->getAction());

        $view = "{$controller}/{$action}.phtml";

        $view   = (isset($options['view']))     ? $options['view']      : $view;
        $layout = (isset($options['layout']))   ? $options['layout']    : "layout";

        if(!file_exists(APPLICATION_PATH.DS.'views'.DS.'layouts'.DS.$layout.'.phtml')){
            throw new \Exception($layout.' layout not found!!');
        }

        if(!file_exists(APPLICATION_PATH.DS.'views'.DS.$view)){
            throw new \Exception($view.' view not found!!');
        }

        $this->_smarty->display('extends:layouts'.DS.$layout.'.phtml|'.$view);
    }
    
    private function json($options) {
        header('Content-Type: application/json; charset=utf-8');
        
        $options = empty($options) ? true : $options; 

        echo json_encode($options);
    }
    
    private function xml() {
        header("Content-Type: application/xml; charset=utf-8");

        echo '<?xml version="1.0" encoding="utf-8" ?>';
        echo '<?xml-stylesheet type="text/css" href="/stylesheets/xml.css" ?>';

        $controller = strtolower( $this->_request->getController() );

        $action = str_replace("Action", "", $this->_request->getAction());

        $view = "{$controller}/{$action}.phtml";

        $this->_smarty->display($view);
    }

    public function linkTo($routeName) {
        return $this->_router->_linkTo($routeName);
    }
    
    public function getParam($param) {
        return $this->_request->_getParams($param);
    }
    
    public function getSmarty() {
        return $this->_smarty;
    }
    
}