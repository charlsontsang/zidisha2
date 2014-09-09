@extends('borrower.loan.loan-base')

@section('content')
@parent

<div class="row">
    <div class="col-xs-12">
        <div class="callout callout-success">
            <h4>@lang('borrower.dashboard.loan-open.fully-funded.title')</h4>
            <p>
                @lang('borrower.loan.loan-open.fully-funded.instructions')
            </p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        @if($loan->isFullyFunded())
            <h2>@lang('borrower.loan.accept-bids.title')</h2>
    
            <div class="alert alert-warning" role="alert">
                @lang('borrower.loan.accept-bids.instructions')
            </div>

            @include('borrower.loan.partials.loan-information-fundraising', compact('loan', 'installmentCalculator'))
    
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
            
            <h2>@lang('borrower.loan.loan-open.details')</h2>
    
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
            <h2>@lang('borrower.loan.lenders')</h2>
            
            @include('partials.loan-lenders', compact($lenders))
        @endif

    </div>
</div>

@stop
