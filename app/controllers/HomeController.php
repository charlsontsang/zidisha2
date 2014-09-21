<?php

use Zidisha\Country\Country;
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
        $country = Utility::getCountryCodeByIP();

        $country = CountryQuery::create()->findOneById($country['id']);

        if ($country && $country->isBorrowerCountry()) {
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

    private function getBorrowerHome(Country $country)
    {
        $exchangeRate = ExchangeRateQuery::create()->findCurrent($country->getCurrency());
        $currency = $country->getCurrency();
        
        $installmentPeriod = $country->getInstallmentPeriod();
        if ($installmentPeriod == Loan::WEEKLY_INSTALLMENT) {
            $period = \Lang::get('borrower.borrow.week');
        } else {
            $period = \Lang::get('borrower.borrow.month');
        }

        $regFee = $country->getRegistrationFee();
        $regFeeNote = '';

        if ($regFee->isPositive()) {
            $regFeeNote = \Lang::get('borrower.borrow.fees-content-part2', ['regFee' => $regFee]);
        }

        $firstLoanValue = Money::create(Setting::get('loan.firstLoanValue'), 'USD');
        $nextLoanValue = Money::create(Setting::get('loan.nextLoanValue'), 'USD');
        $secondLoanPercentage = Setting::get('loan.secondLoanPercentage');
        $nextLoanPercentage = Setting::get('loan.nextLoanPercentage');

        $firstLoanVal = Converter::fromUSD($firstLoanValue, $currency, $exchangeRate);
        $inviteBonus = Money::create(Setting::get('invite.bonus'), 'USD');
        $firstLoanValueInvited = $firstLoanValue->add($inviteBonus);
        $firstLoanValInvited = Converter::fromUSD($firstLoanValueInvited, $currency, $exchangeRate);

        $creditLimitProgression = [];
        $creditLimitProgression[] = $firstLoanVal;
        $value = $firstLoanValue;

        for ($i = 2; $i < 12; $i++) {
            if ($value->lessThanOrEqual(Money::create(200, 'USD'))) {
                $value = $value->multiply($secondLoanPercentage)->divide(100);
            } else {
                $value = $value->multiply($nextLoanPercentage)->divide(100)->min($nextLoanValue);
            }
            $creditLimitProgression[] = Converter::fromUSD($value, $currency, $exchangeRate);
        }

        $advantage3 = \Lang::get('borrower.borrow.advantage3', array('installmentFrequency' => $period));
        $requirementsContentBusiness = \Lang::get('borrower.borrow.requirements-content-business', array('installmentFrequency' => $period));
        $howMuchContent = \Lang::get('borrower.borrow.how-much-content', compact('firstLoanVal', 'firstLoanValInvited'));
        
        return View::make('borrower-home', compact (
            'advantage3', 'requirementsContentBusiness', 'howMuchContent', 'regFeeNote', 'creditLimitProgression'
        ));
    }

}
