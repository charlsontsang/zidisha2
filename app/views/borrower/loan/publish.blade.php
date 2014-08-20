@extends('layouts.master')

@section('page-title')
    @lang("borrower.loan-application.progress-bar.publish-page")
@stop

@section('content')

@include('borrower.loan.partials.application-steps')

<div class="page-header">
    <h1>
        @lang("borrower.loan-application.progress-bar.publish-page")
    </h1>
</div>


<div class="row">
    <h1>Publish Page</h1>
    {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postPublish', 'translationDomain' => 'borrower.loan-publish-page')) }}

   @if($data)
   <p><strong> {{ \Lang::get('borrower.loan-application.publish-loan.amount-requested') }} : </strong> {{ $data['amount'] }} </p> <br>

   <p><strong> {{ \Lang::get('borrower.loan-application.publish-loan.maximum-interest-rate') }} : </strong> {{ $loan->getMaxInterestRate() }} %  </p> <br>

   <p><strong> {{ \Lang::get('borrower.loan-application.publish-loan.monthly-repayment-amount') }} : </strong> {{ $calculator->installmentAmount() }} </p> <br>

   <p><strong> {{ \Lang::get('borrower.loan-application.publish-loan.repayment-period') }} : </strong> {{ $loan->getInstallmentCount() }} </p> <br>

    <p><strong> {{ \Lang::get('borrower.loan-application.publish-loan.maximum-interest-and-transaction-fees') }} : </strong>  {{ $calculator->totalInterest() }} </p> <br>

    <p><strong> {{ \Lang::get('borrower.loan-application.publish-loan.total-repayment-due-date') }} : </strong> {{ $calculator->totalAmount() }} </p> <br>

   @endif

    <p>
        {{ \Lang::get('borrower.loan-application.publish-loan.loan-confirmation-instructions') }}
    </p>

    <p>
        {{ \Lang::get('borrower.loan-application.publish-loan.loan-confirmation') }}
    </p>


    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>{{ \Lang::get('borrower.loan-application.publish-loan.table.due-date') }}</th>
            <th>{{ \Lang::get('borrower.loan-application.publish-loan.table.repayment-due') }}</th>
            <th>{{ \Lang::get('borrower.loan-application.publish-loan.table.balance-remaining') }}</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 0;
                $totalAmount = $calculator->totalAmount();
            ?>
            @foreach($installments as $installment)
                <tr>
                    <td>{{ $i }}</td>
                    <?php $totalAmount = $totalAmount->subtract($installment->getAmount()) ?>
                    <td>{{ $installment->getAmount() }}</td>
                    <td>{{ $totalAmount }}</td>
                    <?php $i++; ?>
                </tr>
            @endforeach

            <tr>
                <td> <strong>{{ \Lang::get('borrower.loan-application.publish-loan.table.total-repayment') }}</strong> </td>
                <td> <strong> {{  $calculator->totalAmount() }} </strong> </td>
            </tr>
        </tbody>
    </table>

    <div class="col-md-7">
        <a href="{{ action('LoanApplicationController@getApplication') }}" class="btn btn-primary">
            Previous
        </a>
    </div>
    <div class="col-md-5">

        {{ BootstrapForm::submit('save') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop