<?php

namespace Zidisha\Loan\Paginator;


use Propel\Runtime\Collection\Collection;
use Zidisha\Lender\Lender;

abstract class AbstractBids implements \IteratorAggregate
{
    /**
     * @var Lender
     */
    protected $lender;

    /**
     * @var int
     */
    protected $page;

    protected $bids;

    protected $count;

    protected $total;

    public function __construct(Lender $lender, $page = 1)
    {
        $this->lender = $lender;
        $this->page = $page;
        
        list($bids, $total) = $this->getResults();
        
        if (is_array($bids)) {
            $this->bids = $bids;
            $this->count = count($bids);
        } else {
            /** @var Collection $bids */
            $this->bids = $bids->getData();
            $this->count = $bids->count();
        }
        
        $this->total = $total;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->bids);
    }
    
    protected abstract function getResults();
    
    public function getBids() {
        return $this->bids;
    }

    public function count()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }
    
    public function getPaginator($pageName = 'page')
    {
        $paginatorFactory = \App::make('paginator');
        $paginatorFactory->setPageName($pageName);

        return $paginatorFactory->make(
            $this->bids,
            $this->total,
            10
        );
    }
} 