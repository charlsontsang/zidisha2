<?php

namespace Zidisha\Lender\Form;


use Zidisha\Balance\TransactionQuery;
use Zidisha\Form\AbstractForm;

class WithdrawFundsForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'paypalEmail'    => 'required|email',
            'withdrawAmount' => 'required|numeric|max:'. $this->getCurrentBalance()->getAmount(),
        ];
    }

    protected function getCurrentBalance()
    {
        $currentBalance = TransactionQuery::create()
            ->filterByUserId(\Auth::id())
            ->getTotalAmount();

        return $currentBalance;
    }
}