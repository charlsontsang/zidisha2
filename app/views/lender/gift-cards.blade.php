@extends('layouts.master')

@section('page-title')
Gift Cards
@stop

@section('content')
<div class="page-header">
    <h1>Gift Cards</h1>
</div>
<div class="page-header">
    <p>{{ \Lang::get('text.gift-cards.gift-card-text') }}</p>
</div>

<div>
    <div>
        <div>

            {{ BootstrapForm::open(array('controller' => 'LenderController@postGiftCards', 'translationDomain' =>
            'lender.gift-cards')) }}
            {{ BootstrapForm::populate($form) }}

            <div style='margin-right: 20px; margin-left: 10px; margin-top:30px;font-size:20px'><strong>
                    <br/><br/>Step One: Select An Image</strong>
            </div>
            {{ BootstrapForm::select('template', $form->getTemplates()) }}
            <br/><br/>


            <div style='margin-right: 20px; margin-left: 10px; margin-top:30px;font-size:20px'><strong>
                    <br/><br/>Step Two: Customize Gift Card</strong>
            </div>
            <br/>


            {{ BootstrapForm::select('amount', $form->getAmounts()) }}

            {{ BootstrapForm::select( 'orderType', $form->getOrderTypes()) }}

            {{ BootstrapForm::text('recipientEmail') }}

            <br/>
            <br/>
            {{ BootstrapForm::label('Optional Fields') }}
            <br/> <br/>
            {{ BootstrapForm::text('recipientName') }}

            {{ BootstrapForm::text('fromName') }}

            {{ BootstrapForm::textarea('message') }}

            {{ BootstrapForm::text('confirmationEmail') }}

            {{ BootstrapForm::submit('save') }}

            {{ BootstrapForm::close() }}

            @stop
