<?php

namespace Hideks\Config;

class Ini {
    
    private $config = array();
    
    public function __construct($file) {
        $configArr = parse_ini_file($file, true);
        
        $this->parseAdvanced($configArr);
    }
    
    private function parseAdvanced($configArr) {
        if( is_array($configArr) ){
            foreach($configArr as $property => $value){
                $e = explode(':', $property);
                
                if( !empty($e[1]) ){
                    $sections = array();
                    
                    foreach($e as $index => $section){
                        $sections[$index] = trim($section);
                    }
                    
                    $sections = array_reverse($sections, true);
                    
                    foreach($sections as $p => $v){
                        $section = $sections[0];
                        
                        if( empty($this->config[$section]) ){
                            $this->config[$section] = array();
                        }
                        
                        if( isset($this->config[$sections[1]]) ){
                            $this->config[$section] = array_merge($this->config[$section], $this->config[$sections[1]]);
                        }
                        
                        if( $p === 0 ){
                            $this->config[$section] = array_merge($this->config[$section], $configArr[$property]);
                        }
                    }
                } else {
                    $this->config[$property] = $configArr[$property];
                }
            }
        }
        
        return $this;
    }
    
    private function parseRecursive($configArr = null) {
        $configArr = ( is_null($configArr) ) ? $this->config : $configArr;
        
        $configs = array();
        
        if( is_array($configArr) ){
            foreach($configArr as $property => $value){
                if( is_array($value) ){
                    $configArr[$property] = $this->parseRecursive($value);
                }
                
                $section = explode('.', $property);
                
                if( !empty($section[1]) ){
                    $section = array_reverse($section, true);
                    
                    if( !isset($configs[$section[0]]) ){
                        $configs[$section[0]] = array();
                    }
                    
                    $first = true;
                    
                    foreach($section as $p => $v){
                        if( $first === true ){
                            $comp = $configArr[$property];
                            $first = false;
                        }

                        $comp = array($v => $comp);
                    }
                    
                    $configs[$section[0]] = array_merge_recursive($configs[$section[0]], $comp[$section[0]]);
                } else {
                    $configs[$property] = $configArr[$property];
                }
            }
        }
        
        return $configs;
    }
    
    public function get() {
        return $this->parseRecursive();
    }
    
}