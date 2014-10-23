<?php

use Hideks\Controller;
use Hideks\Paginator;

class Paginator extends Controller {
    
    public function indexAction() {
        $page = $this->getParam('page', 1);
        
        $paginator = new Paginator(array(
            'totalItens'    => User::count(),
            'currentPage'   => $page,
            'limitPerPage'  => 2
        ));
        
        /* Create a custom pagination with the getAttributes method */
//        var_export($paginator->getAttributes());
        
        /* Basic desktop pagination */
//        $pagination = $paginator->desktop('route_pagination');
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        /* Desktop pagination with aditional params */
//        $pagination = $paginator->desktop('other_route_pagination', array(
//            'params' => array(
//                'name' => 'value'
//            )
//        ));
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        /* Desktop pagination with alternative route name */
//        $pagination = $paginator->desktop(array(
//            'routes' => array(
//                'route',
//                'other_route_pagination'
//            )
//        ));
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        /* Desktop pagination with all options */
//        $pagination = $paginator->desktop(array(
//            'routes' => array(
//                'route',
//                'other_route_pagination'
//            ),
//        ), array(
//            'params' => array(
//                'name' => 'value'
//            )
//        ));
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        /* Basic mobile pagination */
//        $pagination = $paginator->mobile('route_pagination');
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        /* Mobile pagination with aditional params */
//        $pagination = $paginator->mobile('other_route_pagination', array(
//            'params' => array(
//                'name' => 'value'
//            )
//        ));
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        /* Mobile pagination with alternative route name */
//        $pagination = $paginator->mobile(array(
//            'routes' => array(
//                'route',
//                'other_route_pagination'
//            )
//        ));
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        /* Mobile pagination with all options */
//        $pagination = $paginator->mobile(array(
//            'routes' => array(
//                'route',
//                'other_route_pagination'
//            )
//        ), array(
//            'params' => array(
//                'name' => 'value'
//            )
//        ));
//        
//        $this->getSmarty()->assign('pagination', $pagination);
        
        $options = array(
            'limit'     => $paginator->getLimit(),
            'offset'    => $paginator->getOffset()
        );
        
        $users = array();

        foreach (User::all($options) as $user) {
            $users[] = array(
                'id'    => $user->id,
                'name'  => $user->name
            );
        }
        
        $this->getSmarty()->assign('users', $users);
        
        $this->renderTo('html');
    }
    
}