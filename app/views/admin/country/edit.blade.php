@extends('layouts.master')

@section('page-title')
Edit Country
@stop

@section('content')
<h1>{{ $country->getName() }}</h1>
<hr/>
<div class="row">
    {{ BootstrapForm::open(['route' => ['admin:post:edit:country', $country->getId()]]) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::label('Is this a Borrower Country') }}

    {{ BootstrapForm::radio('borrower_country', '1', null, ['label' => 'Yes']) }}
    {{ BootstrapForm::radio('borrower_country', '0', null, ['label' => 'No']) }}

    {{ BootstrapForm::text('dialing_code', null, ['label' => 'Dialing Code']) }}

    {{ BootstrapForm::text('phone_number_length', null, ['label' => 'Phone Number Length']) }}

    {{ BootstrapForm::text('registration_fee', null, ['label' => 'Registration Fee', 'prepend' => $form->getCurrency()]) }}

    {{ BootstrapForm::select('installment_period', $form->getInstallmentPeriods(), $form->getDefaultInstallmentPeriod(), ['label' => 'Installment Period']) }}

    {{ BootstrapForm::textarea('repayment_instructions', null, ['label' => 'Repayment Instructions', 'description' => "This instruction is included in disbursement confirmation emails sent to borrowers and displayed in borrowers' accounts."]) }}

    {{ BootstrapForm::textarea('accept_bids_note', null, ['label' => 'Accept Bid Notes']) }}

    {{ BootstrapForm::submit('Submit') }}

    {{ BootstrapForm::close() }}
</div>
@stop
