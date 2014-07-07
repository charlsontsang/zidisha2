@extends('layouts.master')

@section('page-title')
Edit Country
@stop

@section('content')
<div class="row">
    {{ BootstrapForm::open(['route' => ['admin:post:edit:country', $country->getId()]]) }}
    {{ BootstrapForm::populate($form) }}


    {{ BootstrapForm::label('Borrower Country') }}
    {{ BootstrapForm::radio('borrower_country', true, null, ['label' => 'Yes']) }}
    {{ BootstrapForm::radio('borrower_country', false, null, ['label' => 'No']) }}

    {{ BootstrapForm::text('dialing_code', null, ['label' => 'Dialing Code']) }}

    {{ BootstrapForm::text('phone_number_length', null, ['label' => 'Phone Number Length']) }}

    {{ BootstrapForm::text('registration_fee', null, ['label' => 'Registration Fee']) }}

    {{ BootstrapForm::select('installment_period', $form->getInstallmentPeriods(), $form->getDefaultInstallmentPeriod(), ['label' => 'Installment Period']) }}

    {{ BootstrapForm::textarea('repayment_instructions', null, ['label' => 'Repayment Instructions']) }}

    {{ BootstrapForm::submit('Submit') }}

    {{ BootstrapForm::close() }}
</div>
@stop
