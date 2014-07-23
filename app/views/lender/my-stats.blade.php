@extends('layouts.master')

@section('page-title')
My Stats
@stop

@section('content')
<div class="page-header">
    <h2>My Stats</h2>
</div><br>

<div class="div-header">
    <h2>My Lending Account</h2>
</div><br>

<div class="row">
    <div class="col-xs-4">
        <p>Total Funds Uploaded: <a href="#" class="funds-upload" data-toggle="tooltip">(?)</a>  </p>
        <p>Number of Loans Made:  </p>
        <p>Current Credit Available: <a href="#" class="credit-available" data-toggle="tooltip">(?)</a>  </p>
        <p>New Member Invite Credit: </p>
        <p>Principal Outstanding: <a href="#" class="principal-outstanding" data-toggle="tooltip">(?)</a>  </p>
    </div>

    <div class="col-xs-8">

    </div>
</div>

<div class="div-header">
    <h2>My Network</h2>
</div><br>

<div class="row">
    <div class="col-xs-4">
        <p>My Lending Groups:  </p>
        <p>Number of Invites Sent: </p>
        <p>Number of Invites Accepted: </p>
        <p>Number of Loans Made By My Invitees: </p>
        <p>Number of Gift Cards Gifted: </p>
        <p>Number of Gift Cards Redeemed by My Recipients: </p>
        <p>Number of Loans Made By My Gift Card Recipients: </p>
    </div>

    <div class="col-xs-8">

    </div>
</div>


@stop

@section('script-footer')
<script type="text/javascript">
    $('.funds-upload').tooltip({placement: 'bottom', title: 'The total amount of funds you have uploaded into your account as lending credit. Does not include loan repayments credited to your account.'})
</script>
<script type="text/javascript">
    $('.credit-available').tooltip({placement: 'bottom', title: 'The current balance of credit available for lending, composed of lender fund uploads and repayments received, which have not been withdrawn or bid on new loans. Does not include amounts in your Lending Cart.'})
</script>
<script type="text/javascript">
    $('.principal-outstanding').tooltip({placement: 'bottom', title: 'The portion of US dollar amounts you have lent which is still outstanding with the borrowers (not yet repaid). This amount does not include any interest which is due for the loans, and its value is not adjusted for credit risk or exchange rate fluctuations.'})
</script>
@stop