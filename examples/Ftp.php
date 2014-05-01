<?php

class Ftp extends \Hideks\Controller {
    
    public function indexAction() {
        // Efetua a conexão com o servidor de ftp
        $ftp = new \Hideks\Ftp(array(
            'hostname' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password'
        ));
        
        // Seleciona o diretório
        $ftp->setDirectory('/directory/path/');
        
        // Envia o arquivo para o servidor de ftp
        $ftp->setFile('filename.jpg', $_FILES['file']['tmp_name'], FTP_BINARY);
        
        // Lista o diretório
        $ftp->listDirectory('/directory/path/');
        
        // Deleta o arquivo do diretório
        $ftp->delete('/file/path/');
        
        // Finaliza a conexão com o servidor de ftp
        $ftp->close();
    }
    
}