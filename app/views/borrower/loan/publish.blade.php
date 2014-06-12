@extends('layouts.master')

@section('content')

@include('borrower.loan.partials.application-steps')

Publish Page


<div class="row">
    {{ BootstrapForm::open(array('controller' => 'LoanApplicationController@postPublish', 'translationDomain' => 'borrower.loan-publish-page')) }}

   @if($data)
   <p><strong>Amount: </strong> {{ $data['amount'] }} </p> <br>

   <p><strong>installment Amount: </strong> {{ $data['installmentAmount'] }} </p> <br>

    <p><strong>installment Day: </strong> {{ $data['installmentDay'] }} </p> <br>
   @else
   <p>Wrong Username!</p>
   @endif

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