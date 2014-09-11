@extends('layouts.side-menu')

@section('page-title')
Account Preferences
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')
{{ BootstrapForm::open(array('route' => 'lender:post:preference')) }}
{{ BootstrapForm::populate($form) }}

<h4>Notification Preferences</h4>

<p>Send me an email when:</p>

{{ BootstrapForm::checkbox('notifyLoanFullyFunded', null, null, [
    'label' => 'A loan I have bid on is fully funded',
]) }}

{{ BootstrapForm::checkbox('notifyLoanAboutToExpire', null, null, [
    'label' => 'A loan I have bid on has not been fully funded and is about to expire',
]) }}

<!-- TODO 
{{ BootstrapForm::checkbox('', null, null, [
    'label' => 'A loan I have bid on has expired and the funds have been returned to my account',
]) }}
-->

{{ BootstrapForm::checkbox('notifyLoanDisbursed', null, null, [
    'label' => 'A loan I have funded is disbursed to a borrower',
]) }}

{{ BootstrapForm::checkbox('notifyComment', null, null, [
    'label' => 'A borrower I have funded posts a comment',
]) }}

{{ BootstrapForm::checkbox('notifyLoanApplication', null, null, [
    'label' => 'A borrower I have funded before posts a new loan application',
]) }}

{{ BootstrapForm::checkbox('notifyInviteAccepted', null, null, [
    'label' => 'One of my new lender invites is accepted',
]) }}
<br/>
<p>I would like to be notified about repayments:</p>

{{ BootstrapForm::select( 'notifyLoanRepayment', $form->getNotifyLoanRepayment(), null, ['label' => '']) }}

<br/>

<h4>Display Preferences</h4>

{{ BootstrapForm::checkbox('hideLendingActivity', null, null, [
    'label' => 'I would like my loan bids and funded loans to be displayed on my public profile.',
]) }}

{{ BootstrapForm::checkbox('hideKarma', null, null, [
    'label' => 'I would like my karma score to be displayed on my public profile.',
]) }} 

<br/>

{{ BootstrapForm::submit('Save') }}
{{ BootstrapForm::close() }}
@stop
