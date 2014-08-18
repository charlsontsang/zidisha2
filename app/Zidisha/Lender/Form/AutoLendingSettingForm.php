<?php
namespace Zidisha\Lender\Form;

use Zidisha\Form\AbstractForm;
use Zidisha\Lender\AutoLendingSettingQuery;
use Zidisha\Lender\Lender;

class AutoLendingSettingForm extends AbstractForm
{
    public function getRules($data)
    {
        return [
            
        ];
    }

    public function getDefaultData()
    {
        /** @var Lender $lender */
        $lender = \Auth::user()->getLender();
        
        $autoLendingPreferences = AutoLendingSettingQuery::create()
            ->findOneByLenderId($lender->getId());
        
        return [
            
        ];
    }
} 
