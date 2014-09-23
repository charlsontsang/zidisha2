@extends('borrower.loan.loan-base')

@section('content')
@parent

@if(!$loan->isFullyFunded())
    @include('borrower.dashboard.partials.loan-open-tips')
@endif

<div class="panel panel-info">
@if($loan->isFullyFunded())

    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.loan.accept-bids.title')
        </h3>
    </div>
    <div class="panel-body">
        <p>
            @lang('borrower.loan.accept-bids.instructions')
        </p>

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
    </div>

@else

    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.loan.loan-open.details')
        </h3>
    </div>
    <div class="panel-body">
        
        @include('loan/partials/progress', ['loan' => $loan, 'dollar' => false])

        @include('borrower.loan.partials.loan-information', [
            'amount'            => $loan->getAmount(),
            'maxInterestRate'   => $loan->getMaxInterestRate(),
            'period'            => $loan->getPeriod(),
            'totalInterest'     => $installmentCalculator->totalInterest()->round(2),
            'totalAmount'       => $installmentCalculator->totalAmount()->round(2),
            'loan'              => $loan,
        ])
    </div>
    
@endif
</div>

@if($lenders->count())
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.loan.lenders')
        </h3>
    </div>
    <div class="panel-body">
            @include('loan.partials.lenders', compact($lenders))
    </div>
</div>
@endif

@stop
