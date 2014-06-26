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
            <div style='margin-right: 20px; margin-left: 10px; margin-top:30px;font-size:20px'><strong>
                    <br/><br/>Step One: Select An Image</strong>
            </div>
            <br/><br/>

            {{ Form::label('panda_colour', 'Images') }}
            {{ Form::radio('panda_colour', 'red', true) }} Red
            {{ Form::radio('panda_colour', 'black') }} Black
            {{ Form::radio('panda_colour', 'white') }} White


            <div style='margin-right: 20px; margin-left: 10px; margin-top:30px;font-size:20px'><strong>
                    <br/><br/>Step Two: Customize Gift Card</strong>
            </div>
            <br/>

            {{ BootstrapForm::open(array('controller' => 'LenderController@postGiftCards', 'translationDomain' =>
            'lender.gift-cards')) }}
            {{ BootstrapForm::populate($form) }}

            {{ BootstrapForm::select('amount', $form->getAmounts()) }}

            {{ Form::label('self-Print','Self-Print') }}
            {{ Form::radio('deliveryMethod','Self-Print','',array('id'=>'self-Print')) }}
            {{ Form::label('email','Email') }}
            {{ Form::radio('deliveryMethod','Email','',array('id'=>'email')) }}
            {{ BootstrapForm::text('recipientEmail') }}

            <br/>
            <br/>
            {{ BootstrapForm::label('Optional Fields') }}
            <br/> <br/>
            {{ BootstrapForm::text('toName') }}

            {{ BootstrapForm::text('fromName') }}

            {{ BootstrapForm::textarea('message') }}

            {{ BootstrapForm::text('yourEmail') }}

            {{ BootstrapForm::submit('save') }}

            {{ BootstrapForm::close() }}

            @stop
