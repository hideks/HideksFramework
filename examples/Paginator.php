<?php

class Paginator extends \Hideks\Controller {
    
    public function indexAction() {
        $page = $this->getParam('page');
        
        $page = ($page) ? $page : 1;
        
        $options = array(
            'select'        => 'name, lastname',
            'conditions'    => 'active = 1'
        );
        
        $paginator = new \Hideks\Paginator();
        $paginator->setTotalItens(User::count($options));
        $paginator->setCurrentPage($page);
        $paginator->setLimitPerPage(10);
        $paginator->paginate();
        
        $options += array(
            'limit'     => $paginator->getLimit(),
            'offset'    => $paginator->getOffset()
        );
        
        $users = array();
        
        foreach(User::all($options) as $user){
            $users[] = array(
                'name' => $user->name
            );
        }
        
        $this->getSmarty()->assign('users', $users);
        
        $pagination = $paginator->pagination('names_pagination');
        
        $this->getSmarty()->assign('pagination', $pagination);
        
        $this->renderTo('html');
    }
    
}