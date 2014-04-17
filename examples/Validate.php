<?php

class Validate extends \Hideks\Controller {
    
    public function indexAction() {
        $form = filter_input_array(INPUT_POST);
        
        $username = new \Hideks\Validate($form['username'], 'usuário');
        $username->isNotEmpty()
                 ->isLessThan(3)
                 ->isData('Usuario', 'us_nome', true);
        
        $password = new \Hideks\Validate($form['password'], 'senha');
        $password->isNotEmpty()
                 ->isLessThan(1);
        
        $email = new \Hideks\Validate($form['email'], 'e-mail');
        $email->isNotEmpty()
              ->isEmail();
        
        $created = new \Hideks\Validate($form['created'], 'criado em');
        $created->isNotEmpty()
                ->isInRegex('/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/', 'dd/mm/aaaa');
        
        if( $username->isValid() && $password->isValid()
            && $email->isValid() && $created->isValid() ) {
            $json = 'Formulário validado com sucesso!!';
        } else {
            $json = array(
                "errors" => array(
                    $username->getErrors(),
                    $password->getErrors(),
                    $email->getErrors(),
                    $created->getErrors()
                )
            );
        }
        
        $this->renderTo('json', $json);
    }
    
}