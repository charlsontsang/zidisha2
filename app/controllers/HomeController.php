<?php

use Zidisha\Country\CountryQuery;
use Zidisha\User\User;
use Zidisha\Utility\Utility;
use Zidisha\Loan\Loan;
use Zidisha\Admin\Setting;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Currency\Converter;

class HomeController extends BaseController {

    public function  __construct(
        \Zidisha\Loan\LoanService $loanService
    )
    {
        $this->loanService = $loanService;
    }

    public function getHome()
    {
        $countryCode = Utility::getCountryCodeByIP();

        $country = CountryQuery::create()
            ->findOneByCountryCode($countryCode);
        if($country && $country->isBorrowerCountry()) {
            return $this->getBorrowerHome($country);
        }
        return $this->getLenderHome();
    }
    
    public function getLenderHome()
    {
        $estherCaption = 'Lend <span class="text-primary">Esther</span> $50 to open a grocery shop';
        $fatouCaption = 'Lend <span class="text-primary">Fatou</span> $100 to open a beauty salon';
        $melitaCaption = 'Lend <span class="text-primary">Melita</span> $100 for a dairy cow';
        $binetaCaption = 'Lend <span class="text-primary">Bineta</span> $60 for a sewing machine';
        $maryCaption = 'Lend <span class="text-primary">Mary</span> $50 for a delivery wagon';
        
        $secondaryCaption = 'and join the global <strong>person-to-person</strong> microlending movement.';
        $buttonText = 'Browse Projects';
        $buttonTextBottom = 'Start exploring projects';
        $buttonLink = route('lend:index');
        
        $conditions['status'] = Loan::OPEN;
        $conditions['categoryId'] = '18';
        $projects = $this->loanService->searchLoans($conditions)->take(3);

        return View::make('lender-home', compact('estherCaption', 'fatouCaption', 'melitaCaption', 'binetaCaption', 'maryCaption', 'secondaryCaption','buttonText', 'buttonTextBottom', 'buttonLink', 'projects'));
    }

    private function getBorrowerHome($country)
    {
        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($country->getCurrency());
        $currency = $country->getCurrency();
        
        $installmentPeriod = $country->getInstallmentPeriod();
        if ($installmentPeriod == 'weekly') {
            $period = \Lang::get('borrower.borrow.week');
        } else {
            $period = \Lang::get('borrower.borrow.month');
        }

        $regFee = $country->getRegistrationFee();
        if ($regFee > 0) {
            $regFeeNote = \Lang::get('borrower.borrow.fees-content-part2', array('regFee' => $currency." ".$regFee));
        } else {
            $regFeeNote = '';
        }

        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $nextLoanValue = Money::create(Setting::get('loan.nextLoanValue'), 'USD');
        $secondLoanPercentage = Setting::get('loan.secondLoanPercentage');
        $nextLoanPercentage = Setting::get('loan.nextLoanPercentage');
        
        /* TO DO: Comment out these hard-coded values once Setting::get is defined */
        $firstLoanValue = Money::create('50', 'USD');
        $inviteBonus = Money::create('100', 'USD');
        $firstLoanValueInvited = $firstLoanValue->add($inviteBonus);
        $nextLoanValue = Money::create('10000', 'USD');
        $secondLoanPercentage = 300;
        $nextLoanPercentage = 150;

        $params['firstLoanVal'] = Converter::fromUSD($firstLoanValue, $currency, $exchangeRate);
        $firstLoanValInvited = Converter::fromUSD($firstLoanValueInvited, $currency, $exchangeRate);
        $params['nxtLoanvalue'] = '';
        $value = $firstLoanValue;

        for ($i = 2; $i < 12; $i++) {
            if ($value->lessThanOrEqual(Money::create(200, 'USD'))) {
                $value = $value->multiply($secondLoanPercentage)->divide(100);
                $localValue= Converter::fromUSD($value, $currency, $exchangeRate);
                $params['nxtLoanvalue'] .= "<br/>".$i.". ".' '.$localValue;
            } else {
                $value = $value->multiply($nextLoanPercentage)->divide(100);

                if ($value->lessThanOrEqual($nextLoanValue)) {
                    $localValue = Converter::fromUSD($value, $currency, $exchangeRate);
                    $params['nxtLoanvalue'] .="<br/>".$i.". ".' '.$localValue;
                } else {
                    $value = $nextLoanValue;
                    $localValue= Converter::fromUSD($value, $currency, $exchangeRate);
                    $params['nxtLoanvalue'] .= "<br/>".$i.". ".' '.$localValue;
                }
            }
        }

        $advantage3 = \Lang::get('borrower.borrow.advantage3', array('installmentFrequency' => $period));
        $requirementsContentBusiness = \Lang::get('borrower.borrow.requirements-content-business', array('installmentFrequency' => $period));
        $howMuchContent = \Lang::get('borrower.borrow.how-much-content', array('firstLoanVal' => $params['firstLoanVal'], 'firstLoanValInvited' => $firstLoanValInvited));
            
        return View::make('borrower-home', compact ('advantage3', 'requirementsContentBusiness', 'howMuchContent', 'regFeeNote', 'params'));
    }

}
