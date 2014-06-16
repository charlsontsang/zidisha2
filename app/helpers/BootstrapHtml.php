<?php


class BootstrapHtml {
    
    public static function paginator($paginator) {
        $paginatorFactory = App::make('paginator');
        
        return $paginatorFactory->make(
            $paginator->getResults()->getData(),
            $paginator->getNbResults(),
            $paginator->getMaxPerpage()
        );
    }

} 