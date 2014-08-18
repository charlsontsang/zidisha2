<?php
namespace Zidisha\Lender\Form;

use Zidisha\Form\AbstractForm;
use Zidisha\Lender\AutoLendingSetting;
use Zidisha\Lender\AutoLendingSettingQuery;
use Zidisha\Lender\Lender;

class AutoLendingSettingForm extends AbstractForm
{
    public function getRules($data)
    {
        return [
            'active'                   => 'required',
            'minimumInterestRate'      => 'numeric|min:0|max:100',
            'minimumInterestRateOther' => 'numeric|min:0|max:100|required:minimumInterestRate,other',
            'maximumInterestRate'      => 'numeric|min:0|max:100',
            'maximumInterestRateOther' => 'numeric|min:0|max:100|required:maximumInterestRate,other',
            'preference'               => 'required|numeric|in:' . AutoLendingSetting::AUTO_LEND_AS_PREV_LOAN,
            AutoLendingSetting::LOAN_RANDOM,
            AutoLendingSetting::HIGH_NO_COMMENTS,
            AutoLendingSetting::HIGH_OFFER_INTEREST,
            AutoLendingSetting::EXPIRE_SOON,
            AutoLendingSetting::HIGH_FEEDBCK_RATING
        ];
    }

    public function getDefaultData()
    {
        /** @var Lender $lender */
        $lender = \Auth::user()->getLender();

        $autoLendingPreferences = AutoLendingSettingQuery::create()
            ->findOneByLenderId($lender->getId());

        if ($autoLendingPreferences) {
            return [
                'active'              => $autoLendingPreferences->getActive(),
                'minimumInterestRate' => $autoLendingPreferences->getMinDesiredInterest(),
                'maximumInterestRate' => $autoLendingPreferences->getMaxDesiredInterest(),
                'preference'          => $autoLendingPreferences->getPreference()
            ];
        } else {
            return [
                'active'              => true,
                'minimumInterestRate' => 0,
                'maximumInterestRate' => 0,
                'preference'          => 0
            ];
        }
    }
} 
