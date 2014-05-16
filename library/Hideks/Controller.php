<?php

namespace Hideks;

class Controller {
    
    private $_router;
    
    private $_request;
    
    private $_smarty;
    
    private $controller;
    
    private $action;
    
    private $params;

    public function __construct($frontController) {
        $this->_router = $frontController->getRouter();
        
        $this->_request = $frontController->getRequest();
        
        $this->_smarty = $frontController->getSmarty();
        
        $this->controller = $this->_request->getController();

        $this->action = $this->_request->getAction();
        
        $this->params = $this->_request->getParams();
    }
    
    public function getRequest() {
        return $this->_request;
    }
    
    public function getSmarty() {
        return $this->_smarty;
    }
    
    public function getParam($param, $value = false) {
        return $this->_request->getParam($param, $value);
    }
    
    public function linkTo($routeName, array $params = array()) {
        if( !$this->_router->getRoutes()->exists($routeName) ){
            throw new \Exception("No route with the name $routeName has been found!!");
        }
        
        return $this->_router->linkTo($routeName, $params);
    }
    
    public function renderTo($output, $options = null) {
        if( !is_writeable(APPLICATION_PATH.DS.'temp'.DS.'cached'.DS) ){
            throw new \Exception('Unable to write to <span class="glyphicon glyphicon-home"></span> /application/temp/cached/');
        }
        
        if( !is_writeable(APPLICATION_PATH.DS.'temp'.DS.'compiled'.DS) ){
            throw new \Exception('Unable to write to <span class="glyphicon glyphicon-home"></span> /application/temp/compiled/');
        }
        
        switch($output){
            case 'html':
                header('Content-Type: text/html; charset=utf-8');

                $layout = isset($options['layout']) ? $options['layout'] : 'layout';

                if( !file_exists(APPLICATION_PATH.DS.'views'.DS.'layouts'.DS.$layout.'.phtml') ){
                    throw new \Exception($layout.' layout not found!!');
                }
                
                $this->_smarty->setCacheLifetime(isset($options['expiresAt']) ? $options['expiresAt'] : 3600);

                if(isset($options['noview']) && $options['noview'] === true){
                    return $this->_smarty->display('layouts'.DS.$layout.'.phtml');
                }
                
                $view = $this->controller.DS.$this->action;

                $view = isset($options['view']) ? $options['view'] : $view;

                if( !file_exists(APPLICATION_PATH.DS.'views'.DS.$view.'.phtml') ){
                    throw new \Exception($view.' view not found!!');
                }
                
                $this->_smarty->display('extends:layouts'.DS.$layout.'.phtml|'.$view.'.phtml', $this->generateUniquePageId());
                break;
            case 'json':
                header('Content-Type: application/json; charset=utf-8');
        
                $options = is_null($options) ? true : $options; 

                echo json_encode($options);
                break;
            case 'xml':
                header('Content-Type: application/xml; charset=utf-8');

                echo '<?xml version="1.0" encoding="utf-8" ?>';
                echo '<?xml-stylesheet type="text/css" href="/stylesheets/xml.css" ?>';

                $this->_smarty->display($this->controller.DS.$this->action.'.phtml');
                break;
            case 'txt':
                header('Content-Type: text/plain; charset=utf-8');
                
                foreach($options as $line){
                    echo $line."\n";
                }
                break;
            default:
                throw new \Exception('The output: '.$output.' is not supported!!');
        }
    }
    
    public function renderFromCache($options = null) {
        $layout = isset($options['layout']) ? $options['layout'] : 'layout';

        if(isset($options['noview']) && $options['noview'] === true){
            $template = 'layouts'.DS.$layout.'.phtml';
        } else {
            $template = 'extends:layouts'.DS.$layout.'.phtml|'.$this->controller.DS.$this->action.'.phtml';
        }
        
        if( isset($options['clear']) && $options['clear'] === true ){
            $this->_smarty->clearCache($template, $this->generateUniquePageId());
        }

        if( $this->_smarty->isCached($template, $this->generateUniquePageId()) ) {
            $this->_smarty->display($template, $this->generateUniquePageId());
            
            exit();
        }
    }
    
    private function generateUniquePageId() {
        $hash = "{$this->controller}_{$this->action}";
        
        foreach($this->params as $param){
            if(!is_array($param)){
                $hash .= "_{$param}";
            }
        }
        
        return $hash;
    }
    
}