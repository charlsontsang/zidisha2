@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.publish-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>
        @lang("borrower.loan-application.title.publish-page")
    </h1>
</div>

{{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postPublish', 'translationDomain' => 'borrower.loan-application.publish')) }}
<div class="row">
    <div class="col-md-10">

        <p>
            @lang('borrower.loan-application.publish.intro')
        </p>

        <br/>

        @include('borrower.loan.partials.loan-information', [
            'amount'            => $loan->getAmount(),
            'maxInterestRate'   => $loan->getMaxInterestRate(),
            'installmentAmount' => $data['installmentAmount'],
            'period'            => $loan->getPeriod(),
            'totalInterest'     => $calculator->totalInterest()->round(2),
            'totalAmount'       => $calculator->totalAmount()->round(2),
            'loan'              => $loan,
        ])

        <p>
            @lang('borrower.loan-application.publish.confirmation-instructions')
        </p>

        <p>
            @lang('borrower.loan-application.publish.confirmation', [
                'previous' => \Lang::get('borrower.loan-application.publish.previous'),
                'publish'  => \Lang::get('borrower.loan-application.publish.submit'),
            ])
        </p>    

        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>{{ \Lang::get('borrower.loan.repayment-schedule.due-date') }}</th>
                <th>{{ \Lang::get('borrower.loan.repayment-schedule.repayment-due', ['currencyCode' => $loan->getCurrencyCode()]) }}</th>
                <th>{{ \Lang::get('borrower.loan.repayment-schedule.balance-remaining') }}</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    $i = 0;
                    $totalAmount = $calculator->totalAmount();
                ?>
                @foreach($installments as $installment)
                    @if($i)
                    <tr>
                        <td>{{ $i }}</td>
                        <?php $totalAmount = $totalAmount->subtract($installment->getAmount()) ?>
                        <td>{{ $installment->getAmount()->round(2)->getAmount() }}</td>
                        <td>{{ $totalAmount->round(2)->getAmount() }}</td>
                    </tr>
                    @endif
                    <?php $i++; ?>
                @endforeach
    
                <tr>
                    <td> <strong>{{ \Lang::get('borrower.loan.repayment-schedule.total-repayment') }}</strong> </td>
                    <td> <strong> {{  $calculator->totalAmount()->round(2)->getAmount() }} </strong> </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">

    <div class="col-xs-6">
        <a href="{{ action('LoanApplicationController@getApplication') }}" class="btn btn-primary">
            @lang('borrower.loan-application.publish.previous')
        </a>
    </div>

    <div class="col-xs-6">
        <div class="pull-right">
            {{ BootstrapForm::submit('submit') }}
        </div>
    </div>
</div>
@stop