@extends('borrower.loan.loan-base')

@section('content')
@parent

<div class="row">
    <div class="col-xs-12">
        <div class="callout callout-success">
            <h4>Congratulations, your loan is fully funded!</h4>
            <p>
                you can accept the bids below.
            </p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        @if($loan->isFullyFunded())
            <h2>Accept bids</h2>
    
            <div class="alert alert-warning" role="alert">
                @lang('borrower.loan.accept-bids.instructions')
            </div>
    
            <table class="table table-2-col">
                <tbody>
                <tr>
                    <td>
                        <strong>@lang('borrower.loan.requested-amount'):</strong>
                    </td>
                    <td>
                        {{ $loan->getAmount() }}
                    </td>
                </tr>
    
                <tr>
                    <td>
                        <strong>@lang('borrower.loan.repayment-period'):</strong>
                    </td>
                    <td>
                        {{ $loan->getPeriod() }}
                    </td>
                </tr>
    
                <tr>
                    <td>
                        <strong>@lang('borrower.loan.final-lender-interest-rate'):</strong>
                    </td>
                    <td>
                        {{ $loan->getLenderInterestRate() }}%
                    </td>
                </tr>
    
                <tr>
                    <td>
                        <strong>@lang('borrower.loan.service-fee-rate'):</strong>
                    </td>
                    <td>
                        {{ $loan->getServiceFeeRate() }}%
                    </td>
                </tr>
    
                @if($loan->getRegistrationFee()->isPositive())
                <tr>
                    <td>
                        <strong>@lang('borrower.loan.registration-fee'):</strong>
                    </td>
                    <td>
                        {{ $loan->getRegistrationFee() }}
                    </td>
                </tr>
                @endif
    
                <tr>
                    <td>
                        <strong>@lang('borrower.loan.total-interest-and-fees'):</strong>
                    </td>
                    <td>
                        {{ $installmentCalculator->totalInterest()->round(2) }}
                        ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.loan.weekly-interest-rate' : 'borrower.loan.monthly-interest-rate', [
                        'interestRate' => $loan->getLenderInterestRate() + $loan->getServiceFeeRate(),
                        'period' => $loan->getPeriod(),
                        ]) }})
                    </td>
                </tr>
    
                <tr>
                    <td>
                        <strong>@lang('borrower.loan.total-amount'):</strong>
                    </td>
                    <td>
                        {{ $installmentCalculator->totalAmount()->round(2) }}
                    </td>
                </tr>
                </tbody>
            </table>
    
            <p>
                @lang('borrower.loan.accept-bids.schedule')
            </p>

            @include('borrower.loan.partials.repayment-schedule-installments', compact('repaymentSchedule'))

            {{ BootstrapForm::open([
                'action' => ['BorrowerLoanController@postAcceptBids', $loan->getId()],
                'translationDomain' => 'borrower.loan.accept-bids'
            ]) }}
    
            {{ BootstrapForm::textarea('acceptBidsNote', null, [
                'label' => false,
                'description' => $borrower->getCountry()->getAcceptBidsNote() ?: Lang::get('borrower.loan.accept-bids.default-note'),
                'rows' => 5,
            ]) }}
    
            {{ BootstrapForm::submit('submit') }}
    
            {{ BootstrapForm::close() }}
        @else
        
            @include('partials/loan-progress', ['loan' => $loan, 'dollar' => false])
            
            <h2>Loan details</h2>
    
            @include('borrower.loan.partials.loan-information', [
                'amount'            => $loan->getAmount(),
                'maxInterestRate'   => $loan->getMaxInterestRate(),
                'period'            => $loan->getPeriod(),
                'totalInterest'     => $installmentCalculator->totalInterest()->round(2),
                'totalAmount'       => $installmentCalculator->totalAmount()->round(2),
                'loan'              => $loan,
            ])
        
        @endif
    </div>
    <div class="col-sm-6">
        @if(!$loan->isFullyFunded())
            @include('borrower.dashboard.loan-open-tips')
        @endif
        
        @if($lenders->count())
            <h2>Lenders</h2>
            
            @include('partials.loan-lenders', compact($lenders))
        @endif

    </div>
</div>

@stop
