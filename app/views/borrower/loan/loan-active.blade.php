@extends('borrower.loan.loan-base')

@section('content')
@parent

@if($repaymentSchedule->getNextDueInstallment())
    <?php $nextInstallment = $repaymentSchedule->getNextDueInstallment(); ?>
    <div>
        <p>
            {{ \Lang::get('borrower.loan.active.next-installment') }}:
            <table class="table">
                <thead>
                <tr>
                    <th colspan="2">Expected Payments</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $nextInstallment->getDueDate()->format('M j, Y') }}</td>
                    <td>{{ $nextInstallment->getAmount() }}</td>
                </tr>
                </tbody>
            </table>
        </p>
    </div>
@endif
<br><br>

@include('borrower.loan.partials.repayment-schedule')

@stop
