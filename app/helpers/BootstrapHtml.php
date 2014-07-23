<?php


class BootstrapHtml {
    
    public static function paginator($paginator, $pageName = 'page') {
        $paginatorFactory = App::make('paginator');
        $paginatorFactory->setPageName($pageName);
        
        return $paginatorFactory->make(
            $paginator->getResults()->getData(),
            $paginator->getNbResults(),
            $paginator->getMaxPerpage()
        );
    }

} 