<?php

namespace Hideks\Validate;

abstract class ValidateAbstract {
    
    protected $value  = null;
    
    protected $label  = null;
    
    protected $errors = null;
    
    public function __construct($value, $label) {
        $this->value = $value;
        
        $this->label = $label;
    }
    
    public function getErrors() {
        return $this->errors;
    }

    protected function setErrors($errors) {
        $this->errors = $errors;
        
        return $this;
    }
    
}