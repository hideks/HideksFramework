<?php

namespace Hideks;

use DOMDocument;
use Exception;
use Hideks\Router;

final class Paginator {
    
    private $router = null;
    
    private $attributes = array();
    
    private $limit = null;
    
    private $offset = null;
    
    private $routes = array();

    private $params = array();
    
    private $dom_doc = null;
    
    public function __construct() {
        $args = func_get_arg(0);
        
        if( !isset($args['totalItens']) ){
            throw new Exception('The configuration array should have a "totalItens" index');
        }
        
        if( !isset($args['currentPage']) ){
            throw new Exception('The configuration array should have a "currentPage" index');
        }
        
        $this->router = Router::parseFile();
        
        $this->totalItens   = $args['totalItens'];
        
        $this->currentPage  = $args['currentPage'];
        
        $this->limitPerPage = !isset($args['limitPerPage']) ? 10 : $args['limitPerPage'];
        
        $this->paginate();
    }
    
    public function __get($name) {
        return $this->attributes[$name];
    }
    
    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function getAttributes() {
        return $this->attributes;
    }
    
    public function getOffset() {
        return $this->offset;
    }

    public function getLimit() {
        return $this->limit;
    }
    
    private function paginate() {
        $this->totalPages = (int) ceil($this->totalItens / $this->limitPerPage);

        $this->currentPage = (int) min(max(1, $this->currentPage), max(1, $this->totalPages));

        $this->firstItem = (int) min((($this->currentPage - 1) * $this->limitPerPage) + 1, $this->totalItens);

        $this->lastItem = (int) min($this->firstItem + $this->limitPerPage - 1, $this->totalItens);

        $this->previousPage = ($this->currentPage > 1) ? $this->currentPage - 1 : false;

        $this->nextPage = ($this->currentPage < $this->totalPages) ? $this->currentPage + 1 : false;

        $this->firstPage = ($this->currentPage === 1) ? false : 1;

        $this->lastPage = ($this->currentPage >= $this->totalPages) ? false : $this->totalPages;

        $this->before = (($this->currentPage - 4) < 1) ? 1 : $this->currentPage - 4;

        $this->after = (($this->currentPage + 4) > $this->totalPages) ? $this->totalPages : $this->currentPage + 4;

        $this->offset = (int) (($this->currentPage - 1) * $this->limitPerPage);

        $this->limit = $this->limitPerPage;
    }
    
    public function __call($function, $args) {
        if(empty($args)){
            throw new Exception('No pagination route was detected!!');
        }
        
        if(isset($args[0]) && is_array($args[0])){
            if(isset($args[0]['routes']) && $this->is_routes($args[0]['routes'])){
                $this->routes = array(
                    $args[0]['routes'][0],
                    $args[0]['routes'][1]
                );
            }
        } else {
            $this->routes = array(
                str_replace('_pagination', '', $args[0]),
                $args[0]
            );
        }
        
        if(isset($args[1]) && is_array($args[1])){
            if(isset($args[1]['params'])){
                $this->params = $args[1]['params'];
            }
        }
        
        if($this->totalPages <= 1){
            return false;
        }
        
        if( !class_exists("DOMDocument") ){
            throw new Exception("PHP DOM is not installed or enabled on this server!!");
        }
        
        $this->dom_doc = new DOMDocument();
        
        return $this->$function();
    }
    
    private function is_routes($routes) {
        foreach($routes as $route){
            if (!$this->router->getRoutes()->exists($route)) {
                throw new Exception("No route with the name $route has been found!!");
            }
        }
        
        return true;
    }
    
    private function desktop() {
        $ul = $this->createElement($this->dom_doc, 'ul', array(
            'attributes' => array(
                'class' => 'pagination'
            )
        ));
        
        if($this->firstPage){
            $this->leftArrowButton($ul);
        } else {
            $this->leftArrowButtonDisabled($ul);
        }
        
        if( $this->firstPage && ($this->currentPage - 4) > $this->firstPage){
            $this->firstPageButton($ul);
        }
        
        if( ($this->currentPage - 4) > ($this->firstPage + 1) ){
            $this->ellipsis($ul);
        }
        
        $this->sequence($ul);
        
        if( ($this->currentPage + 4) < ($this->lastPage - 1) ){
            $this->ellipsis($ul);
        }
        
        if( $this->lastPage && ($this->currentPage + 4) < $this->lastPage ){
            $this->lastPageButton($ul);
        }
        
        if($this->lastPage){
            $this->rightArrowButton($ul);
        } else {
            $this->rightArrowButtonDisabled($ul);
        }
            
        return $this->dom_doc->saveHTML();
    }
    
    private function mobile() {
        $ul = $this->createElement($this->dom_doc, 'ul', array(
            'attributes' => array(
                'class' => 'pagination'
            )
        ));
        
        if($this->firstPage){
            $this->leftArrowButton($ul);
        } else {
            $this->leftArrowButtonDisabled($ul);
        }
        
        $current = $this->createElement($ul, 'li', array(
            'attributes' => array(
                'class' => 'active'
            )
        ));
        
        $this->createElement($current, 'span', array(
            'text' => $this->currentPage . ' / ' . $this->totalPages
        ));
        
        if($this->lastPage){
            $this->rightArrowButton($ul);
        } else {
            $this->rightArrowButtonDisabled($ul);
        }
        
        return $this->dom_doc->saveHTML();
    }
    
    private function createElement($parent, $tag, $options = array()) {
        $el = $this->dom_doc->createElement($tag);
        
        if(isset($options['attributes'])){
            foreach($options['attributes'] as $attr => $val){
                if(!empty($val)){
                    $el->setAttribute($attr, $val);
                }
            }
        }
        
        if(isset($options['text'])){
            $text = $this->dom_doc->createTextNode($options['text']);
            
            $el->appendChild($text);
        }
        
        $parent->appendChild($el);
        
        return $el;
    }
    
    private function leftArrowButton($parent) {
        $prev = $this->createElement($parent, 'li');
            
        $isPageTwo = $this->currentPage === 2 ? 1 : 0; 

        $options = array();

        if(!$isPageTwo){
            $options['page'] = $this->previousPage;
            $options += $this->params;
        }

        $prevLink = $this->createElement($prev, 'a', array(
            'attributes' => array(
                'href'  => $this->router->linkTo($this->routes[!$isPageTwo], $options),
                'rel'   => $isPageTwo ? 'start prev' : 'prev'
            )
        ));

        $this->createElement($prevLink, 'span', array(
            'attributes' => array(
                'class' => 'glyphicon glyphicon-chevron-left'
            )
        ));
    }
    
    private function rightArrowButton($parent) {
        $next = $this->createElement($parent, 'li');
            
        $options = array(
            'page' => $this->nextPage
        );
        
        $options += $this->params;
        
        $nextLink = $this->createElement($next, 'a', array(
            'attributes' => array(
                'href'  => $this->router->linkTo($this->routes[1], $options),
                'rel'   => 'next'
            )
        ));

        $this->createElement($nextLink, 'span', array(
            'attributes' => array(
                'class' => 'glyphicon glyphicon-chevron-right'
            )
        ));
    }
    
    private function leftArrowButtonDisabled($parent) {
        $next = $this->createElement($parent, 'li', array(
            'attributes' => array(
                'class' => 'disabled'
            )
        ));
            
        $nextLink = $this->createElement($next, 'a', array(
            'attributes' => array(
                'href'  => '#'
            )
        ));

        $this->createElement($nextLink, 'span', array(
            'attributes' => array(
                'class' => 'glyphicon glyphicon-chevron-left'
            )
        ));
    }
    
    private function rightArrowButtonDisabled($parent) {
        $next = $this->createElement($parent, 'li', array(
            'attributes' => array(
                'class' => 'disabled'
            )
        ));
            
        $nextLink = $this->createElement($next, 'a', array(
            'attributes' => array(
                'href'  => '#'
            )
        ));

        $this->createElement($nextLink, 'span', array(
            'attributes' => array(
                'class' => 'glyphicon glyphicon-chevron-right'
            )
        ));
    }
    
    private function sequence($parent) {
        for ($i = $this->before; $i <= $this->after; $i++) {
            if($this->currentPage === $i){
                $current = $this->createElement($parent, 'li', array(
                    'attributes' => array(
                        'class' => 'active'
                    )
                ));
                
                $this->createElement($current, 'span', array(
                    'text' => $i
                ));
            } else if($this->previousPage === $i) {
                $previous = $this->createElement($parent, 'li');
                
                $options = array(
                    'page' => $i
                );
                
                $options += $this->params;
                
                $this->createElement($previous, 'a', array(
                    'attributes' => array(
                        'href' => $this->router->linkTo($this->routes[1], $options),
                        'rel' => $this->previousPage === 1 ? 'start prev' : 'prev'
                    ),
                    'text' => $i
                ));
            } else if($this->nextPage === $i) {
                $next = $this->createElement($parent, 'li');
                
                $options = array(
                    'page' => $i
                );
                
                $options += $this->params;
                
                $this->createElement($next, 'a', array(
                    'attributes' => array(
                        'href' => $this->router->linkTo($this->routes[1], $options),
                        'rel' => 'next'
                    ),
                    'text' => $i
                ));
            } else if($this->firstPage === $i) {
                $first = $this->createElement($parent, 'li');
                
                $this->createElement($first, 'a', array(
                    'attributes' => array(
                        'href' => $this->router->linkTo($this->routes[0]),
                        'rel' => 'start'
                    ),
                    'text' => $i
                ));
            } else {
                $rest = $this->createElement($parent, 'li');
                
                $options = array(
                    'page' => $i
                );
                
                $options += $this->params;
                
                $this->createElement($rest, 'a', array(
                    'attributes' => array(
                        'href' => $this->router->linkTo($this->routes[1], $options)
                    ),
                    'text' => $i
                ));
            }
        }
    }
    
    private function firstPageButton($parent) {
        $first = $this->createElement($parent, 'li');
        
        $this->createElement($first, 'a', array(
            'attributes' => array(
                'href'  => $this->router->linkTo($this->routes[0]),
                'rel'   => 'start'
            ),
            'text' => $this->firstPage
        ));
    }
    
    private function lastPageButton($parent) {
        $last = $this->createElement($parent, 'li');
        
        $options = array(
            'page' => $this->lastPage
        );
        
        $options += $this->params;
            
        $this->createElement($last, 'a', array(
            'attributes' => array(
                'href'  => $this->router->linkTo($this->routes[1], $options)
            ),
            'text' => $this->lastPage
        ));
    }
    
    private function ellipsis($parent) {
        $ellipsis = $this->createElement($parent, 'li', array(
            'attributes' => array(
                'class' => 'disabled'
            )
        ));

        $this->createElement($ellipsis, 'span', array(
            'text' => '...'
        ));
    }
    
}