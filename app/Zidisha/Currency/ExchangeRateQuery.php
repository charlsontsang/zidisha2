<?php

namespace Zidisha\Currency;

use Zidisha\Currency\Base\ExchangeRateQuery as BaseExchangeRateQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'exchange_rates' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ExchangeRateQuery extends BaseExchangeRateQuery
{

    public function filterByDate(\DateTime $date)
    {
        return $this
            ->condition('start', 'ExchangeRate.StartDate <= ?', $date)
            ->condition('endDate', 'ExchangeRate.EndDate > ?', $date)
            ->condition('endNull', 'ExchangeRate.EndDate IS NULL')
            ->combine(['endDate', 'endNull'], 'or', 'end')
            ->where(['start', 'end'], 'and');
    }

    public function findCurrent(Currency $currency)
    {
        return $this
            ->filterByCurrencyCode($currency->getCode())
            ->filterByDate(new \DateTime())
            ->findOne();
    }
    
} // ExchangeRateQuery
