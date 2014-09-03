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

    public static function tooltip($tag, $parameters = [])
    {
        $title = \Lang::get($tag, $parameters);

        return '<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="'.$title.'"></i>';
    }

} 