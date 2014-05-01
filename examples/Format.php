<?php

class Format extends \Hideks\Controller {
    
    public function indexAction() {
        // Formatando a string para o seo
        echo \Hideks\Format::string('TeStE dE StRiNg')->toSeo();
        
        // Formatando a string para o gravar no banco de dados
        echo \Hideks\Format::string('TeStE dE \'StRiNg')->toPreventInjection();
        
        // Formatando os objetos para uma string separada por vírgulas
        $users = Usuario::all();
        
        echo \Hideks\Format::objects($users)->toStringByColumn('us_nome');
    }
    
}