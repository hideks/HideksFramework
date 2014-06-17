<?php

namespace Hideks;

class Validate extends Validate\ValidateAbstract {

    public function isNotEmpty() {
        if (!is_null($this->getErrors())) {
            return;
        }

        if (empty($this->value)) {
            $this->setErrors("O campo \"{$this->label}\" deve ser preenchido!");
        }

        return $this;
    }

    public function isNumber() {
        if (!is_null($this->getErrors())) {
            return;
        }

        if (!is_numeric($this->value)) {
            $this->setErrors("O campo \"{$this->label}\" deve ser um número!");
        }

        return $this;
    }

    public function isLessThan($value) {
        if (!is_null($this->getErrors())) {
            return;
        }

        if (strlen($this->value) < $value) {
            $this->setErrors("O campo \"{$this->label}\" deve possuir no mínimo {$value} caracteres!");
        }

        return $this;
    }

    public function isInRegex($regex, $format) {
        if (!is_null($this->getErrors())) {
            return;
        }

        if (empty($regex)) {
            throw new \Exception('The argument 1 passed to isInRegex() must be a valid regular expression!!');
        }

        if (!preg_match($regex, $this->value)) {
            if (is_null($format)) {
                throw new \Exception('The argument 2 passed to isInRegex() must not be null!!');
            }

            $this->setErrors("O campo \"{$this->label}\" deve estar no seguinte formato: {$format}.");
        }

        return $this;
    }

    public function isInArray($options) {
        if (!is_null($this->getErrors())) {
            return;
        }

        $control = false;

        foreach ($options as $option) {
            if ($option === $this->value) {
                $control = true;
            }
        }

        if ($control === false) {
            $this->setErrors("O valor do campo {$this->label} ({$this->value}) não é válido!");
        }

        return $this;
    }

    public function isEmail() {
        if (!is_null($this->getErrors())) {
            return;
        }

        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->setErrors("O endereço de e-mail \"{$this->value}\" não é válido!");
        }

        $domain = explode("@", $this->value);

        if (isset($domain[1]) && !checkdnsrr($domain[1])) {
            $this->setErrors("O domínio \"{$domain[1]}\" não existe!");
        }

        return $this;
    }

    public function isDate() {
        if (!is_null($this->getErrors())) {
            return;
        }
        
        $this->isInRegex('/^([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])$/', 'AAAA-MM-DD');
        
        $this->value = explode("-", $this->value);

        if (count($this->value) === 3) {
            list($y, $m, $d) = $this->value;

            if (!checkdate($m, $d, $y)) {
                $this->setErrors("O campo \"{$this->label}\" possui uma data inválida!");
            }
        } else {
            $this->setErrors("O campo \"{$this->label}\" está em um formato inválido!");
        }

        return $this;
    }
    
    public function isTime($seconds = false) {
        if (!is_null($this->getErrors())) {
            return;
        }
        
        if($seconds){
            $this->isInRegex('/^([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', 'HH:MM:SS');
        } else {
            $this->isInRegex('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', 'HH:MM');
        }
        
        return $this;
    }
    
    public function isDomain() {
        if (!is_null($this->getErrors())) {
            return;
        }

        $this->value = str_replace('http://', '', $this->value);

        if (!filter_var(gethostbyname($this->value), FILTER_VALIDATE_IP)) {
            $this->setErrors("O {$this->label} \"{$this->value}\" não é válido!");
        }

        return $this;
    }

    public function isData($model, $column, $shouldExists = true) {
        if (!is_null($this->getErrors())) {
            return;
        }

        if (!is_string($model)) {
            throw new \Exception('The argument 1 passed to isData() must be a string!!');
        }

        if (!file_exists(APPLICATION_PATH . DS . 'models' . DS . $model . '.php')) {
            throw new \Exception("$model model not found!!");
        }

        $data = $model::find(array(
                    'select' => $column,
                    'conditions' => "{$column} = '{$this->value}'"
        ));

        if (!$shouldExists && !empty($data)) {
            $this->setErrors("O {$this->label} \"{$this->value}\" já encontra-se cadastrado!");
        }

        if ($shouldExists && empty($data)) {
            $this->setErrors("O {$this->label} \"{$this->value}\" não encontra-se cadastrado!");
        }

        return $this;
    }

    public function isValid() {
        if ($this->getErrors()) {
            return false;
        }

        return true;
    }

}