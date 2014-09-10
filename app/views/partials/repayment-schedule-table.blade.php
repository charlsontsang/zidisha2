<?php
if (!empty($dollarExchangeRate)) {
    $c = function($amount) use($dollarExchangeRate) {
        return \Zidisha\Currency\Converter::toUSD($amount, $dollarExchangeRate);
    };
} else {
    $c = function($amount) {
        return $amount;
    };
}
?>

<table class="table">
    <thead>
    <tr>
        <th colspan="2">@lang('borrower.loan.repayment-schedule.expected-payments')</th>
        <th colspan="2">@lang('borrower.loan.repayment-schedule.actual-payments')</th>
    </tr>
    </thead>
    <tbody>
    @foreach($repaymentSchedule as $repaymentScheduleInstallment)
    @if(!$repaymentScheduleInstallment->getInstallment()->isGracePeriod())
    <tr>
        <td>{{ $repaymentScheduleInstallment->getInstallment()->getDueDate()->format('M j, Y') }}</td>
        <td>{{ $c($repaymentScheduleInstallment->getInstallment()->getAmount()) }}</td>
        <?php $i = 0; ?>
        @if($repaymentScheduleInstallment->getPayments())
        @foreach($repaymentScheduleInstallment->getPayments() as $repaymentScheduleInstallmentPayment)
        @if($i > 0)
        <tr>
            <td></td>
            <td></td>
            <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
            <td>{{ $c($repaymentScheduleInstallmentPayment->getAmount()) }}</td>
        </tr>
        @else
        <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
        <td>{{ $c($repaymentScheduleInstallmentPayment->getAmount()) }}</td>
        </tr>
        @endif
        <?php $i++; ?>
        @endforeach
        @else
        <td></td>
        <td></td>
        @endif
    @endif
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td><strong>@lang('borrower.loan.repayment-schedule.total-amount-due')</strong></td>
        <td><strong>{{ $c($repaymentSchedule->getTotalAmountDue()) }}</strong></td>
        <td><strong>@lang('borrower.loan.repayment-schedule.total-amount-paid')</strong></td>
        <td><strong>{{ $c($repaymentSchedule->getTotalAmountPaid()) }}</strong></td>
    </tr>
    </tfoot>
</table>
