<?php

class Auth extends \Hideks\Controller {
    
    public function loginAction(){
        $data = (object) filter_input_array(INPUT_POST);
        
        $fields = array();

        foreach ($data->form as $field) {
            $fields[$field['name']] = $field['value'];
        }

        $fields = (object) $fields;
        
        $username = new \Hideks\Validate($fields->username, "e-mail");
        $username->isNotEmpty()
                 ->isEmail();
        
        $password = new \Hideks\Validate($fields->password, "senha");
        $password->isNotEmpty()
                 ->isLessThan(1);
        
        if( $username->isValid() && $password->isValid() ){
            $authAdapter = new \Hideks\Auth\Adapter\ContentStuff();
            
            $authAdapter->setEnterpriseId(80)
                        ->setEnterpriseUser('BP_Ass')
                        ->setEnterprisePass('FGUdngw5sfga1As#')
                        ->setEnterpriseProduct(730);
            
            $authAdapter->setUsername($fields->username)
                        ->setPassword($fields->password);
            
            if( $authAdapter->autenticate() ){
                $json = $this->parseResponse( $authAdapter->getResponse() );
            } else {
                $json = false;
            }
            
            if( $json === true ){
                $json = \Hideks\Auth::getInstance()->write($authAdapter);
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
    
    private function parseResponse($response) {
        if( $response['Status_id'] === '1' ){
            return true;
        }
        
        if( $response['Status_id'] === '3' ){
            return array(
                'errors' => array(
                    'Não identificamos um pagamento ativo. Entre em contato conosco...'
                )
            );
        }
        
        \Hideks\Logger::log(array(
            'folder'    => 'content_stuff',
            'title'     => 'Status_id do assinante',
            'content'   => $response,
            'name'      => 'status_id-nao-previsto'
        ));
        
        return array(
            'errors' => array(
                'Houve um problema na comunicação. Tente novamente mais tarde...'
            )
        );
    }
    
    public function logoutAction() {
        \Hideks\Auth::getInstance()->logout();
        
        $this->getRequest()->redirectTo('/');
    }
    
}