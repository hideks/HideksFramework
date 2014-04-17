<?php

namespace Hideks;

class Validate extends Validate\ValidateAbstract {
    
    public function isNotEmpty(){
        if(is_null($this->getErrors())){
            if(empty($this->value)){
                $this->setErrors("O campo \"{$this->label}\" deve ser preenchido!");
            }
            
            return $this;
        }
    }
    
    public function isLessThan($value){
        if(is_null($this->getErrors())){
            if(strlen($this->value) < $value){
                $this->setErrors("O campo \"{$this->label}\" deve possuir no mínimo {$value} caracteres!");
            }
            
            return $this;
        }
    }
    
    public function isInRegex($value, $format){
        if(is_null($this->getErrors())){
            if(empty($value)){
                throw new \Exception('The argument 1 passed to isInRegex() must be a valid regular expression!!');
            }
            
            if(!preg_match($value, $this->value)){
                if(is_null($format)){
                    $this->setErrors("O campo \"{$this->label}\" deve possuir no mínimo {$value} caracteres!");
                } else {
                    $this->setErrors("O campo \"{$this->label}\" deve estar no seguinte formato:<br/>{$format}.");
                }
            }
            
            return $this;
        }
    }
    
    public function isEmail(){
        if(is_null($this->getErrors())){
            if(!filter_var($this->value, FILTER_VALIDATE_EMAIL)){
                $this->setErrors("O endereço de e-mail \"{$this->value}\" não é um endereço de e-mail válido!");
            }
            
            $domain = explode("@", $this->value);
            
            if(isset($domain[1])){
                if(!checkdnsrr($domain[1])){
                    $this->setErrors("O domínio \"{$domain[1]}\" não existe!!");
                }
            }
            
            return $this;
        }
    }
    
    public function isData($model, $column, $shouldExists = true){
        if(is_null($this->getErrors())){
            if( !is_string($model) ){
                throw new \Exception('The argument 1 passed to isData() must be a string!!');
            }
            
            if( !file_exists(APPLICATION_PATH.DS.'models'.DS.$model.'.php') ){
                throw new \Exception("$model model not found!!");
            }
            
            $data = $model::find(array(
                'select'        => $column,
                'conditions'    => "{$column} = '{$this->value}'"
            ));
            
            if( !$shouldExists && !empty($data) ){
                $this->setErrors("O {$this->label} \"{$this->value}\" já encontra-se cadastrado!");
            }
            
            if( $shouldExists && empty($data) ){
                $this->setErrors("O {$this->label} \"{$this->value}\" não encontra-se cadastrado!");
            }
                
            return $this;
        }
    }
    
    public function isValid(){
        if($this->getErrors()){
            return false;
        } else {
            return true;
        }
    }
    
}