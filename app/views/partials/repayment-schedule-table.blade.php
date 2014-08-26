<table class="table">
    <thead>
    <tr>
        <th colspan="2">Expected Payments</th>
        <th colspan="2">Actual Payments</th>
    </tr>
    </thead>
    <tbody>
    @foreach($repaymentSchedule as $repaymentScheduleInstallment)
    <tr>
        <td>{{ $repaymentScheduleInstallment->getInstallment()->getDueDate()->format('M j, Y') }}</td>
        <td>{{ $repaymentScheduleInstallment->getInstallment()->getAmount() }}</td>
        <?php $i = 0; ?>
        @if($repaymentScheduleInstallment->getPayments())
        @foreach($repaymentScheduleInstallment->getPayments() as $repaymentScheduleInstallmentPayment)
        @if($i > 0)
    <tr>
        <td></td>
        <td></td>
        <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
        <td>{{ $repaymentScheduleInstallmentPayment->getAmount() }}</td>
    </tr>
    @else
    <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
    <td>{{ $repaymentScheduleInstallmentPayment->getAmount() }}</td>
    </tr>
    @endif
    <?php $i++; ?>
    @endforeach
    @else
    <td></td>
    <td></td>
    @endif
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td><strong>Total Amount Due</strong></td>
        <td><strong>{{ $repaymentSchedule->getTotalAmountDue() }}</strong></td>
        <td><strong>Total Amount Paid</strong></td>
        <td><strong>{{ $repaymentSchedule->getTotalAmountPaid() }}</strong></td>
    </tr>
    </tfoot>
</table>
