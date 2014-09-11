<?php

namespace Zidisha\Lender\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Lender\Lender;

class AccountPreferencesForm extends AbstractForm
{

    /**
     * @var \Zidisha\Lender\Lender
     */
    private $lender;

    public function __construct(Lender $lender)
    {

        $this->lender = $lender;
    }

    public function getRules($data)
    {
        return [
            'hideLendingActivity'     => '',
            'hideKarma'               => '',
            'notifyLoanFullyFunded'   => '',
            'notifyLoanAboutToExpire' => '',
            'notifyLoanDisbursed'     => '',
            'notifyComment'           => '',
            'notifyLoanApplication'   => '',
            'notifyInviteAccepted'    => '',
            'notifyLoanRepayment'     => 'required|in:' . implode(',', array_keys($this->getNotifyLoanRepayment())),
        ];
    }

    public function getDefaultData()
    {
        $preference = $this->lender->getPreferences();

        return [
            'hideLendingActivity'     => $preference->getHideLendingActivity(),
            'hideKarma'               => $preference->getHideKarma(),
            'notifyLoanFullyFunded'   => $preference->getNotifyLoanFullyFunded(),
            'notifyLoanAboutToExpire' => $preference->getNotifyLoanAboutToExpire(),
            'notifyLoanDisbursed'     => $preference->getNotifyLoanDisbursed(),
            'notifyComment'           => $preference->getNotifyComment(),
            'notifyLoanApplication'   => $preference->getNotifyLoanApplication(),
            'notifyInviteAccepted'    => $preference->getNotifyInviteAccepted(),
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
}
