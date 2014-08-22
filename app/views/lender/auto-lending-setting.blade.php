@extends('layouts.master')

@section('page-title')
Account Preferences
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="page-header">
            <h1>Automated Lending</h1>
        </div>
        <div>
        <p>
        Automated lending allows you to maximize your impact by continuously relending your available lending credit. Talk about paying it forward! <a href="#">Learn more</a>
        </p>
        </div>        
        
        {{ BootstrapForm::open(array('route' => [ 'lender:post:auto-lending', $lender->getId() ], 'translationDomain' => 'lender.auto-lending.preferences')) }}
        {{ BootstrapForm::populate($form) }}
        
        {{ \BootstrapForm::radio('active', 1); }} YES!  Activate automated lending.
        {{ \BootstrapForm::radio('active', 0); }} No thanks, deactivate automated lending.
        
        
        <br/>
        <hr/>
        <p>
        Please set your minimum desired interest rate.  
        </p>
        {{ \BootstrapForm::radio('minimumInterestRate', '0') }} 0%
        {{ \BootstrapForm::radio('minimumInterestRate', '3') }} 3%
        {{ \BootstrapForm::radio('minimumInterestRate', '5') }} 5%
        {{ \BootstrapForm::radio('minimumInterestRate', '10') }} 10%
        {{ \BootstrapForm::radio('minimumInterestRate', 'other') }} Other: {{ \BootstrapForm::text('minimumInterestRateOther') }} 

        <br/>
        <hr/>
        <p>
        Please set your maximum desired interest rate. 
        </p>
        {{ \BootstrapForm::radio('maximumInterestRate', '0') }} 0%
        {{ \BootstrapForm::radio('maximumInterestRate', '3') }} 3%
        {{ \BootstrapForm::radio('maximumInterestRate', '5') }} 5%
        {{ \BootstrapForm::radio('maximumInterestRate', '10') }} 10%
        {{ \BootstrapForm::radio('maximumInterestRate', 'other') }} Other: {{ \BootstrapForm::text('maximumInterestRateOther') }} 

        <br/>
        <hr/>
        <p>
        How would you like your funds to be automatically lent out?  
        </p>
        {{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::HIGH_FEEDBCK_RATING) }}  Give priority to borrowers with highest feedback rating.
        {{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::EXPIRE_SOON) }} Give priority to loans expiring the soonest.
        {{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::HIGH_OFFER_INTEREST) }} Give priority to loans with highest available interest rates.
        {{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::HIGH_NO_COMMENTS) }} Give priority to borrowers with the highest number of comments posted.
        {{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::LOAN_RANDOM) }} Choose loans at random.
        {{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::AUTO_LEND_AS_PREV_LOAN) }} Match loans made manually by other lenders.
                
        @if ($currentBalance->isPositive()) 
            <br/>
            <hr/>
            <p>
                Would you like your current credit balance of <b><i>{{$currentBalance->getAmount()}} {{$currentBalance->getCurrency()}}</i></b> to be automatically allocated to fundraising loans according to these criteria?
            </p>
            {{ \BootstrapForm::radio('currentAllocated', 1) }}  Yes, apply automated lending to both my current balance and to future repayments that are credited to my account.
            {{ \BootstrapForm::radio('currentAllocated', 0) }} No, apply automated lending only to future repayments and leave my current balance available for manual lending.   
        @endif
        <br/>
        <br/>
        <hr/>
        {{ BootstrapForm::submit('save') }}
        {{ BootstrapForm::close() }}
    </div>
</div>
@stop
