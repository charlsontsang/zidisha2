@extends('layouts.master')

@section('page-title')
Account Preferences
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
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
                <a href="#" class="karmaScore" data-toggle="tooltip">(?)</a>
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

@section('script-footer')
<script type="text/javascript">
    $('.karmaScore').tooltip({placement: 'bottom', title: 'Karma is calculated on the basis of the total amount lent by the new members a member has recruited to Zidisha via email invites or gift cards, and the number of comments a member has posted in the Zidisha website.'})
</script>
@stop
