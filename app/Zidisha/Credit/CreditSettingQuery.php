<?php

namespace Zidisha\Credit;

use Zidisha\Borrower\Borrower;
use Zidisha\Country\Country;
use Zidisha\Credit\Base\CreditSettingQuery as BaseCreditSettingQuery;
use Zidisha\Currency\Money;


/**
 * Skeleton subclass for performing query and update operations on the 'credit_settings' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class CreditSettingQuery extends BaseCreditSettingQuery
{

    public function getBorrowerInviteCreditAmount(Country $country)
    {
        $creditSetting = $this
                ->filterByCountry($country)
                ->filterByType(CreditSetting::BORROWER_INVITE_CREDIT)
                ->findOne();

        return Money::create($creditSetting->getLoanAmountLimit(), $country->getCurrency());
    }
} // CreditSettingQuery
