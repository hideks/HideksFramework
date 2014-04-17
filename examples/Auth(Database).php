<?php

class Auth extends \Hideks\Controller {
    
    public function loginAction() {
        $data = (object) filter_input_array(INPUT_POST);
        
        $fields = array();
        
        foreach ($data->form as $field) {
            $fields[$field['name']] = $field['value'];
        }

        $fields = (object) $fields;
        
        $username = new \Hideks\Validate($fields->username, 'usuário');
        $username->isNotEmpty()
                 ->isLessThan(3)
                 ->isData('Usuario', 'us_nome', true);
        
        $password = new \Hideks\Validate($fields->password, 'senha');
        $password->isNotEmpty()
                 ->isLessThan(1);
        
        if($username->isValid() && $password->isValid()){
            $authAdapter = new \Hideks\Auth\Adapter\Database(\ActiveRecord\Connection::instance());
            
            $authAdapter->setUserColumn('us_nome')
                        ->setPassColumn('us_senha')
                        ->setTable('usuarios');
            
            $authAdapter->setUsername($fields->username)
                        ->setPassword(sha1($fields->password));
            
            if( $authAdapter->autenticate() ){
                $json = \Hideks\Auth::getInstance()->write($authAdapter);
            } else {
                $json = false;
            }
        } else {
            $json = array(
                "errors" => array(
                    $username->getErrors(),
                    $password->getErrors()
                )
            );
        }
        
        $this->renderTo("json", $json);
    }
    
    public function logoutAction() {
        \Hideks\Auth::getInstance()->logout();
        
        $this->getRequest()->redirectTo('/');
    }
    
}