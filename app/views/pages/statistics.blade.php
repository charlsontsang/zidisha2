@extends('layouts.master')

@section('page-title')
Statistics
@stop


@section('content')

<div class="page-header">
    <h1>Statistics</h1>
     <br/>
    <h4>Community Statistics</h4>
    What the whole Zidisha community has achieved so far
    <br/><br/>

    Loan money raised:<i class="fa fa-info-circle raisedAmount" data-toggle="tooltip"></i>
    <br/>
    <p>
    USD {{ number_format($totalStatistics['disbursed_amount'], 0, ".", ","); }}
    </p>
    <br/>
    Loan projects funded: <i class="fa fa-info-circle loansFunded" data-toggle="tooltip"></i>
    <br/>
    {{ number_format($totalStatistics['raised_count'], 0, ".", ","); }}
    <br/><br/>
    Lenders joined:
    <br/>
    {{ number_format($totalStatistics['lenders_count'], 0, ".", ","); }}
    <br/><br/>
    Borrowers joined:
    <br/>
    {{ number_format($totalStatistics['borrowers_count'], 0, ".", ",") }}
    <br/><br/>
    Total members:
    <br/>
    {{ number_format($totalStatistics['lenders_count'] + $totalStatistics['borrowers_count'], 0, ".", ",") }}
    <br/><br/>
    Countries represented by Zidisha members:
    <br/>
    {{ number_format($totalStatistics['countries_count'], 0, ".", ",") }}
    <br/><br/>

    <h4>Lending Statistics</h4>
    Use the dropdowns below to get filtered performance statistics for all loans funded since our founding in 2009.
    <br/><br/>
    Display data for loans disbursed within:
    <br/>Display data for loans in:
    <br/>
    Loan money raised: <i class="fa fa-info-circle raisedAmountFiltered" data-toggle="tooltip"></i>
    <br/>
    Loan projects funded: <i class="fa fa-info-circle loansFundedFiltered" data-toggle="tooltip"></i>
    <br/>
    Average lender interest: <i class="fa fa-info-circle lenderInterest" data-toggle="tooltip"></i>
    <br/>
    Principal repaid: <i class="fa fa-info-circle principalRepaid" data-toggle="tooltip"></i>
    <br/>
    Principal held by borrowers repaying on time (within 30-day threshold): <i class="fa fa-info-circle principalRepaidOnTime" data-toggle="tooltip"></i>
    <br/>
    Principal held by borrowers more than 30 days past due with scheduled repayments: <i class="fa fa-info-circle principalRepaidDue" data-toggle="tooltip"></i>
    <br/>
    Principal that has been forgiven by lenders: <i class="fa fa-info-circle principalForgiven" data-toggle="tooltip"></i>
    <br/>
    Principal that has been written off: <i class="fa fa-info-circle principalWrittenOff" data-toggle="tooltip"></i>
    <br/>
    Want to dive deeper? You can see the individual loan reports that provided the raw data for these statistics <a href="https://www.zidisha.org/index.php?p=114">here, TODO</a>.
    <br/><br/>


</div>
@stop

@section('script-footer')
<script type="text/javascript">
    $('.raisedAmount').tooltip({placement: 'bottom', title: 'The cumulative US Dollar amount of loans disbursed'})
</script>
<script type="text/javascript">
    $('.loansFunded').tooltip({placement: 'bottom', title: 'The cumulative number of individual loans funded'})
</script>
<script type="text/javascript">
    $('.raisedAmountFiltered').tooltip({placement: 'bottom', title: 'The total US Dollar amount of loans disbursed in the selected time period and location'})
</script>
<script type="text/javascript">
    $('.loansFundedFiltered').tooltip({placement: 'bottom', title: 'The number of individual loans funded in the selected time period and location'})
</script>
<script type="text/javascript">
    $('.lenderInterest').tooltip({placement: 'bottom', title: 'The average lender interest rate of all loans fully funded by lenders and accepted by borrowers, weighted by the dollar amount of each lender\'s share. Interest is expressed as a flat percentage of loan principal per year the loan is held.'})
</script>
<script type="text/javascript">
    $('.principalRepaid').tooltip({placement: 'bottom', title: 'The principal (not including interest) that has already been repaid to lenders for loans disbursed in the selected time period and location, expressed as a dollar amount and as a percentage of the amount disbursed'})
</script>
<script type="text/javascript">
    $('.principalRepaidOnTime').tooltip({placement: 'bottom', title: 'The principal (not including interest) still held by borrowers who are current or less than 30 days and $10 past due with their scheduled repayment installments, expressed as a dollar amount and as a percentage of the amount disbursed'})
</script>
<script type="text/javascript">
    $('.principalRepaidDue').tooltip({placement: 'bottom', title: 'The principal (not including interest) still held by borrowers who are more than 30 days and $10 past due with their scheduled repayment installments, expressed as a dollar amount and as a percentage of the amount disbursed'})
</script>
<script type="text/javascript">
    $('.principalForgiven').tooltip({placement: 'bottom', title: 'The principal (not including interest) that lenders have elected not to accept as repayments for humanitarian reasons'})
</script>
<script type="text/javascript">
    $('.principalWrittenOff').tooltip({placement: 'bottom', title: 'The principal (not including interest) that has been classified as written off. Zidisha classifies as written off all amounts that have not been repaid six months after a loan\'s final repayment installment due date, and all loans for which the borrower has not made a payment in over six months.  Writing off a loan is a reporting convention, and does not necessarily mean collection efforts stop or that it will not be repaid to lenders.'})
</script>
@stop
