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
            'minimumInterestRate'      => 'CheckInterestRate',
            'minimumInterestRateOther' => 'numeric|min:0|max:100',
            'maximumInterestRate'      => 'CheckInterestRate',
            'maximumInterestRateOther' => 'numeric|min:0|max:100',
            'preference'               => 'required|in:' . implode(',', array_keys($this->getPreferenceArray())),
            'currentAllocated'         => 'required|in:0,1'
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
                'active'              =>  $autoLendingPreferences->getActive(),
                'minimumInterestRate' =>$this->getDefaultInterestRate($autoLendingPreferences->getMinDesiredInterest()),
                'minimumInterestRateOther' =>$this->getOtherInterestRate($autoLendingPreferences->getMinDesiredInterest()),
                'maximumInterestRate' => $this->getDefaultInterestRate($autoLendingPreferences->getMaxDesiredInterest()),
                'maximumInterestRateOther' => $this->getOtherInterestRate($autoLendingPreferences->getMaxDesiredInterest()),
                'preference'          => $autoLendingPreferences->getPreference(),
                'currentAllocated'    => $autoLendingPreferences->getCurrentAllocated()
            ];
        } else {
            return [
                'active'              => 'true',
                'minimumInterestRate' => '0',
                'maximumInterestRate' => '0',
                'preference'          => '1',
                'currentAllocated'    => '1'
            ];
        }
    }

    public function getDefaultInterestRate($interestRate)
    {
        if (in_array($interestRate, [0,3,5,10])) {
            return $interestRate;
        } else {
          return 'other';  
        }
    }

    private function getOtherInterestRate($interestRate)
    {
        if (in_array($interestRate, [0,3,5,10])) {
            return '';
        } else {
            return $interestRate;
        }
    }

    public function getPreferenceArray()
    {
        return [AutoLendingSetting::AUTO_LEND_AS_PREV_LOAN,
            AutoLendingSetting::LOAN_RANDOM,
            AutoLendingSetting::HIGH_NO_COMMENTS,
            AutoLendingSetting::HIGH_OFFER_INTEREST,
            AutoLendingSetting::EXPIRE_SOON,
            AutoLendingSetting::HIGH_FEEDBCK_RATING];
    }
} 
