<?php

namespace Hideks;

class Paginator {
    
    private $currentPage = 0;
    
    private $totalItens = 0;
    
    private $limitPerPage = 10;
    
    private $totalPages;
    
    private $firstItem;
    
    private $lastItem;
    
    private $previousPage;
    
    private $nextPage;
    
    private $firstPage;
    
    private $lastPage;
    
    private $before;
    
    private $after;
    
    private $offset;
    
    private $limit;
    
    public function setCurrentPage($currentPage) {
        $this->currentPage = $currentPage;
    }
    
    public function setTotalItens($totalItens) {
        $this->totalItens = $totalItens;
    }
    
    public function setLimitPerPage($limitPerPage) {
        $this->limitPerPage = $limitPerPage;
    }
    
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    public function getPreviousPage() {
        return $this->previousPage;
    }

    public function getNextPage() {
        return $this->nextPage;
    }

    public function getFirstPage() {
        return $this->firstPage;
    }

    public function getLastPage() {
        return $this->lastPage;
    }

    public function getBefore() {
        return $this->before;
    }

    public function getAfter() {
        return $this->after;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function paginate() {
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
    
    public function pagination($routeName = false, $paramName = 'page') {
        $router = Router::parseFile();
        
        if( !$router->getRoutes()->exists($routeName) ) {
            throw new \Exception("No route with the name $routeName has been found!!");
        }
            
        $pagination = array(
            "currentPage"       => $this->currentPage,
            "totalPages"        => $this->totalPages,
            "firstPage"         => $this->firstPage,
            "previousPage"      => $this->previousPage,
            "nextPage"          => $this->nextPage,
            "lastPage"          => $this->lastPage,
            "before"            => $this->before,
            "after"             => $this->after
        );
        
        if($routeName){
            $sequence = array();

            for($i = $this->before; $i <= $this->after; $i++){
                $sequence[] = $router->linkTo($routeName, array(
                    $paramName => $i
                ));
            }

            $pagination += array(
                "sequence" => $sequence,
                
                "totalPagesUrl" => $router->linkTo($routeName, array(
                    $paramName => $this->totalPages
                )),
                
                "firstPageUrl" => $router->linkTo($routeName, array(
                    $paramName => $this->firstPage
                )),
                
                "previousPageUrl" => $router->linkTo($routeName, array(
                    $paramName => $this->previousPage
                )),
                
                "nextPageUrl" => $router->linkTo($routeName, array(
                    $paramName => $this->nextPage
                )),
                
                "lastPageUrl" => $router->linkTo($routeName, array(
                    $paramName => $this->lastPage
                ))
            );
        }

        return $pagination;
    }
    
}