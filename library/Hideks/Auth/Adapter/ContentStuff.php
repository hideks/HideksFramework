<?php

namespace Hideks\Auth\Adapter;

class ContentStuff extends AdapterAbstract {
    
    private $enterpriseId = null;
    
    private $enterpriseUser = null;
    
    private $enterprisePass = null;
    
    private $enterpriseProduct = null;
    
    private $response = null;
    
    public function setEnterpriseId($enterpriseId) {
        $this->enterpriseId = $enterpriseId;
        
        return $this;
    }
    
    public function setEnterpriseUser($enterpriseUser) {
        $this->enterpriseUser = $enterpriseUser;
        
        return $this;
    }
    
    public function setEnterprisePass($enterprisePass) {
        $this->enterprisePass = $enterprisePass;
        
        return $this;
    }
    
    public function setEnterpriseProduct($enterpriseProduct) {
        $this->enterpriseProduct = $enterpriseProduct;
        
        return $this;
    }
    
    public function getResponse() {
        return $this->response;
    }
    
    public function autenticate() {
        if( !function_exists("curl_init") ){
            throw new \Exception("CURL is not installed or activated on this server!!");
        }
        
        $data = array(
            "EmpresaID"     => $this->enterpriseId,
            "WSUserName"    => $this->enterpriseUser,
            "WSUserPassW"   => $this->enterprisePass,
            "ProdutoAssID"  => $this->enterpriseProduct,
            "Email_txt"     => $this->username,
            "Senha_txt"     => $this->password,
            "UserIP"        => $_SERVER['REMOTE_ADDR'],
            "UDID_txt"      => ""
        );
        
        $data = http_build_query($data);
        
        $url = "https://webservices.assinaja.com/validation/WS_ValidacaoAssDigital.asmx/CSWF_ValidaAcessoAssDigital";
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_exec($curl);

        $this->response = (array) simplexml_load_string( curl_exec($curl) );
        
        curl_close($curl);
        
        if($this->response['Status_id'] === '1'){
            return array(
                'us_nome'   => $this->response['Nome_txt'],
                'us_email'  => $this->username
            );
        }
        
        return false;
    }
    
}