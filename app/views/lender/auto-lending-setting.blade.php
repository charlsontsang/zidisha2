@extends('layouts.side-menu')

@section('page-title')
Autolending
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')
<p>
    Autolending allows you to maximize your impact by continuously relending your available lending credit. Talk about paying it forward!
    &nbsp;&nbsp;&nbsp;                    
    <a href="#" data-toggle="tooltip" data-placement="bottom" title="When you activate automated lending, the credit available in your account will be automatically allocated to new fundraising loans according to the preferences you select.  
        Automated lending takes place once every 24 hours, in increments of $10.  To ensure a broad distribution of your funds, each loan will receive no more than $10 from your account.
        When you upload additional funds to your account, you will be offered the choice to lend them manually to the entrepreneur of your choice, or to have them automatically lent according to your selected parameters.
        You may use this page to deactivate automated lending at any time.">
        Learn more
    </a>    
</p>

{{ BootstrapForm::open(array('route' => [ 'lender:post:auto-lending', $lender->getId() ])) }}
{{ BootstrapForm::populate($form) }}

<h4>Activation</h4>

{{ BootstrapForm::radio('active', 1, null, [
    'label' => 'Activate automated lending.'
]) }}

{{ BootstrapForm::radio('active', 0, null, [
    'label' => 'Dectivate automated lending.'
]) }}

<br/>

<h4>Funds Allocation</h4>
<p>
    Choose the minimum interest rate.  
</p>
{{ \BootstrapForm::radio('minimumInterestRate', '0', null, [
    'label' => '0%'
]) }}
{{ \BootstrapForm::radio('minimumInterestRate', '3', null, [
    'label' => '3%'
]) }} 
{{ \BootstrapForm::radio('minimumInterestRate', '5', null, [
    'label' => '5%'
]) }} 
{{ \BootstrapForm::radio('minimumInterestRate', '10', null, [
    'label' => '10%'
]) }} 
{{ \BootstrapForm::radio('minimumInterestRate', 'other', null, [
    'label' => 'Other:'
]) }} 
{{ \BootstrapForm::text('minimumInterestRateOther', null, [
    'label' => ''
]) }} 

<br/>

<p>
    Choose the maximum interest rate to receive.
</p>
{{ \BootstrapForm::radio('maximumInterestRate', '0', null, [
    'label' => '0%'
]) }}
{{ \BootstrapForm::radio('maximumInterestRate', '3', null, [
    'label' => '3%'
]) }} 
{{ \BootstrapForm::radio('maximumInterestRate', '5', null, [
    'label' => '5%'
]) }} 
{{ \BootstrapForm::radio('maximumInterestRate', '10', null, [
    'label' => '10%'
]) }} 
{{ \BootstrapForm::radio('maximumInterestRate', 'other', null, [
    'label' => 'Other:'
]) }} 
{{ \BootstrapForm::text('maximumInterestRateOther', null, [
    'label' => ''
]) }} 

<br/>

<p>
    How would you like your loans to be chosen?  
</p>

  
{{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::AUTO_LEND_AS_PREV_LOAN, null, [
    'label' => 'Match loans made manually by other lenders.'
]) }}  
{{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::HIGH_NO_COMMENTS, null, [
    'label' => 'Give priority to borrowers with the highest number of comments posted.'
]) }} 
{{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::HIGH_FEEDBCK_RATING, null, [
    'label' => 'Give priority to borrowers with the highest feedback rating.'
]) }}  
{{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::EXPIRE_SOON, null, [
    'label' => 'Give priority to loans about to expire.'
]) }}
{{ \BootstrapForm::radio('preference', \Zidisha\Lender\AutoLendingSetting::LOAN_RANDOM, null, [
    'label' => 'Choose loans at random.'
]) }}  
        
@if ($currentBalance->isPositive()) 
<br/>
<p>
    Would you like your current credit balance of {{$currentBalance->getAmount()}} {{$currentBalance->getCurrency()}} to be automatically allocated to fundraising loans according to these criteria?
</p>
{{ \BootstrapForm::radio('currentAllocated', 1, null, [
    'label' => 'Yes, apply automated lending to both my current balance and to future repayments that are credited to my account.'
]) }}  
{{ \BootstrapForm::radio('currentAllocated', 0, null, [
    'label' => 'No, apply automated lending only to future repayments and leave my current balance available for manual lending.'
]) }}     
@endif

<br/>

{{ BootstrapForm::submit('Save') }}
{{ BootstrapForm::close() }}
@stop
