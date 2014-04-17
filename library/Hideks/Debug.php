<?php

namespace Hideks;

class Debug {

    private $message = null;
    
    private $code = null;
    
    private $file = null;
    
    private $line = null;
    
    private $trace = null;
    
    public function handler(\Exception $exception) {
        $this->message  = $exception->getMessage();
        $this->code     = $exception->getCode();
        $this->file     = $this->getShortFileName($exception->getFile());
        $this->line     = $exception->getLine();
        $this->trace    = $this->parseTrace($exception->getTrace());
        
        $this->dispatch();
    }
    
    private function parseTrace($trace) {
        $routes = array();

        $iteration = count($trace);

        foreach ($trace as $route) {
            if (empty($route['args'])) {
                $route['args'] = "";
            }

            $routes[] = array(
                'iteration' => $iteration--,
                'file' => $this->getShortFileName($route['file']),
                'line' => $route['line'],
                'function' => "{$route['class']}{$route['type']}{$route['function']}({$route['args']})"
            );
        }
        
        return $routes;
    }

    private function getShortFileName($fileName) {
        $blacklist = array(
            'public_html'
        );

        foreach ($blacklist as $item) {
            $fileName = str_replace($item, '', $fileName);
        }

        $delimiters = array(
            'application', 'vendor', 'public'
        );

        foreach ($delimiters as $delimiter) {
            $string = strpos($fileName, $delimiter);

            if ($string) {
                return '<span class="glyphicon glyphicon-home"></span> /' . substr($fileName, $string);
            }
        }
    }

    private function dispatch() {
        $config = \Hideks\Config::getInstance();

        if( $config['system']['environment'] === 'production' ){
            Logger::log(array(
                "folder"    => "production",
                "title"     => null,
                "content"   => array(
                    'ERROR_MESSAGE'     => $this->message,
                    'HTTP_HOST'         => $_SERVER['HTTP_HOST'],
                    'HTTP_USER_AGENT'   => $_SERVER['HTTP_USER_AGENT'],
                    'REMOTE_ADDR'       => $_SERVER['REMOTE_ADDR'],
                    'REQUEST_METHOD'    => $_SERVER['REQUEST_METHOD'],
                    'REQUEST_URI'       => $_SERVER['REQUEST_URI']
                ),
                "format"    => "xml"
            ));
            
            $front = new Application\FrontController();

            $front->setController('Error')
                  ->setAction('indexAction')
                  ->setParams(array(
                      'error_handler' => array(
                          'code'        => ($this->code) ? 404 : 500
                      )
                  ));

            $front->dispatch();
        }
            
        
        if( $config['system']['environment'] === 'development' ){
            require_once(realpath(dirname(__FILE__).DS.'..').DS.'Hideks'.DS.'Debug'.DS.'template.phtml');
        }
    }

}