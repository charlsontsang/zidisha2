<?php

namespace Zidisha\Lender\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Lender\PreferencesQuery;

class AccountPreferencesForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'hideLendingActivity'     => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'hideKarma'               => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'notifyLoanFullyFunded'   => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'notifyLoanAboutToExpire' => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'notifyLoanDisbursed'     => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'notifyComment'           => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'notifyLoanApplication'   => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'notifyInviteAccepted'    => 'required|in:' . implode(',', array_keys($this->getBooleanArray())),
            'notifyLoanRepayment'     => 'required|in:' . implode(',', array_keys($this->getNotifyLoanRepayment())),
        ];
    }

    public function getDefaultData()
    {
        $lender = \Auth::user()->getLender();
        $preference = $lender->getPreferences();

        return [
            'hideLendingActivity'     => $preference->getHideLendingActivity()? 'true' : 'false',
            'hideKarma'               => $preference->getHideKarma()? 'true' : 'false',
            'notifyLoanFullyFunded'   => $preference->getNotifyLoanFullyFunded()? 'true' : 'false',
            'notifyLoanAboutToExpire' => $preference->getNotifyLoanAboutToExpire()? 'true' : 'false',
            'notifyLoanDisbursed'     => $preference->getNotifyLoanDisbursed()? 'true' : 'false',
            'notifyComment'           => $preference->getNotifyComment()? 'true' : 'false',
            'notifyLoanApplication'   => $preference->getNotifyLoanApplication()? 'true' : 'false',
            'notifyInviteAccepted'    => $preference->getNotifyInviteAccepted()? 'true' : 'false',
            'notifyLoanRepayment'     => $preference->getNotifyLoanRepayment(),
        ];
    }

    public function getNotifyLoanRepayment()
    {
        $array = [
            1   => 'Every time I receive a repayment',
            10  => 'When my credit balance reaches $10',
            25  => 'When my credit balance reaches $25',
            50  => 'When my credit balance reaches $50',
            100 => 'When my credit balance reaches $100',
            0   => 'Do not notify me about repayments',
        ];

        return $array;
    }

    public function getBooleanArray()
    {
        return [
            'true'  => 'true',
            'false' => 'false',
        ];
    }
}
