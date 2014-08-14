<?php

namespace Zidisha\Balance;

use Zidisha\Balance\Base\Transaction as BaseTransaction;
use Zidisha\Currency\Money;

class Transaction extends BaseTransaction
{
    const FUND_UPLOAD          = 1;
    const FUND_WITHDRAW        = 2;
    const LOAN_SENT_LENDER     = 3;
    const LOAN_BACK_LENDER     = 4;
    const FEE                  = 5;
    const DISBURSEMENT         = 6;
    const LOAN_BACK            = 7;
    const GIFT_REDEEM          = 8;
    const GIFT_PURCHASE        = 9;
    const GIFT_DONATE          = 10;
    const DONATION             = 11;
    const PAYPAL_FEE           = 12;
    const REFERRAL_DEBIT       = 13;
    const REGISTRATION_FEE     = 14;
    const REFERRAL_CODE        = 15;
    const LOAN_BID             = 16;
    const LOAN_OUTBID          = 17;
    const STRIPE_FEE           = 18;
    const LENDER_INVITE_CREDIT = 19;

    const REFERRAL_CREDIT               = 1;
    const DONATE_BY_ADMIN               = 2;
    const PLACE_BID                     = 3;
    const UPDATE_BID                    = 4;
    const LOAN_BID_EXPIRED              = 5;
    const LOAN_BID_CANCELED             = 6;
    const UPLOADED_BY_ADMIN             = 7;
    const UPLOADED_BY_PAYPAL            = 8;
    const UPLOADED_BY_STRIPE            = 9;
    const LENDER_INVITE_INVITER         = 10;
    const LENDER_INVITE_INVITEE         = 11;
    const LENDER_INVITE_REDEEM          = 12;
    const LENDER_INVITE_REDEEM_EXPIRED  = 13;
    const LENDER_INVITE_RETURN          = 14;

    public function getAmount()
    {
        return Money::create(parent::getAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Transaction
     */
    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }
}
