<?php

class Format extends \Hideks\Controller {
    
    public function indexAction() {
        /* Formatando a string para o seo - Início */
        echo \Hideks\Format::string('TeStE dE StRiNg')->toSeo();
        /* Formatando a string para o seo - Final */
        
        /* Formatando a string para o gravar no banco de dados - Início */
        echo \Hideks\Format::string('TeStE dE \'StRiNg')->toPreventInjection();
        /* Formatando a string para o gravar no banco de dados - Final */
        
        /* Formatando os objetos para uma string separada por vírgulas - Início */
        $users = Usuario::all();
        
        echo \Hideks\Format::objects($users)->toStringByColumn('us_nome');
        /* Formatando os objetos para uma string separada por vírgulas - Final */
    }
    
}