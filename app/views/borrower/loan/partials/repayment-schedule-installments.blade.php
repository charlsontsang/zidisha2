<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>{{ \Lang::get('borrower.loan.repayment-schedule.due-date') }}</th>
        <th>{{ \Lang::get('borrower.loan.repayment-schedule.repayment-due', ['currencyCode' => $repaymentSchedule->getCurrencyCode()]) }}</th>
        <th>{{ \Lang::get('borrower.loan.repayment-schedule.balance-remaining') }}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    $totalAmount = $repaymentSchedule->getTotalAmountDue();
    ?>
    @foreach($repaymentSchedule as $repaymentScheduleInstallment)
    @if($i)
    <tr>
        <td>{{ $i }}</td>
        <?php
            $installment = $repaymentScheduleInstallment->getInstallment();
            $totalAmount = $totalAmount->subtract($installment->getAmount())
        ?>
        <td>{{ $installment->getAmount()->round(2)->getAmount() }}</td>
        <td>{{ $totalAmount->round(2)->getAmount() }}</td>
    </tr>
    @endif
    <?php $i++; ?>
    @endforeach

    <tr>
        <td> <strong>{{ \Lang::get('borrower.loan.repayment-schedule.total-repayment') }}</strong> </td>
        <td> <strong> {{  $repaymentSchedule->getTotalAmountDue()->round(2)->getAmount() }} </strong> </td>
        <td></td>
    </tr>
    </tbody>
</table>