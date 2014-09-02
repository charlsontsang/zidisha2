@extends('layouts.master')

@section('page-title')
Account Preferences
@stop

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
            @include('partials.nav-links.lender-links')       
          </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>Account Preferences</h1>
        </div>
        {{ BootstrapForm::open(array('route' => 'lender:post:preference', 'translationDomain' =>
        'lender.preferences')) }}
        {{ BootstrapForm::populate($form) }}
        <div>
            <h3>Display Preferences</h3>
            <p>I would like my loan bids and funded loans to be displayed on my public profile.</p>
            {{ BootstrapForm::select('hideLendingActivity', $form->getBooleanArray()) }}
            <p>
                I would like my karma score to be displayed on my public profile.
                {{ BootstrapHtml::tooltip('lender.tooltips.preference.karma-score') }}
            </p>{{ BootstrapForm::select('hideKarma', $form->getBooleanArray()) }}
        </div>
        <div>
            <h3>Notification Preferences</h3>
            <p><b>Send me an email when:</b></p>
            <p>A loan I have bid on is fully funded</p>
            {{ BootstrapForm::select( 'notifyLoanFullyFunded', $form->getBooleanArray()) }}
            <p>A loan I have bid on has not been fully funded and is about to expire</p>
            {{ BootstrapForm::select( 'notifyLoanAboutToExpire', $form->getBooleanArray()) }}
            <p>A loan I have bid on has expired and the funds have been returned to my account</p>
            <p>A loan I have funded is disbursed to a borrower</p>
            {{ BootstrapForm::select( 'notifyLoanDisbursed', $form->getBooleanArray()) }}
            <p>A borrower I have funded posts a comment</p>
            {{ BootstrapForm::select( 'notifyComment', $form->getBooleanArray()) }}
            <p>A borrower I have funded before posts a new loan application</p>
            {{ BootstrapForm::select( 'notifyLoanApplication', $form->getBooleanArray()) }}
            <p>One of my friend invites is accepted</p>
            {{ BootstrapForm::select( 'notifyInviteAccepted', $form->getBooleanArray()) }}
            <p>I would like to be notified about repayments:</p>
            {{ BootstrapForm::select( 'notifyLoanRepayment', $form->getNotifyLoanRepayment()) }}
        </div>
        {{ BootstrapForm::submit('save') }}
        {{ BootstrapForm::close() }}
    </div>
</div>
@stop
