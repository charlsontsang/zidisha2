@if($loan->getStatus() >= Zidisha\Loan\Loan::ACTIVE)
    <div>
        <table class="table">
            <thead>
            <tr>
                <th colspan="2">{{ \Lang::get('borrower.loan.partials.expected-payments') }}</th>
                <th colspan="2">{{ \Lang::get('borrower.loan.partials.actual-payments') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($repaymentSchedule as $repaymentScheduleInstallment)
            <tr>
                <td>{{ $repaymentScheduleInstallment->getInstallment()->getDueDate()->format('M j, Y') }}</td>
                <td>{{ $repaymentScheduleInstallment->getInstallment()->getAmount() }}</td>
                <?php $i = 0; ?>
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
                @endif
                <?php $i++; ?>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@elseif($loan->getStatus() == Zidisha\Loan\Loan::FUNDED)
    <div>
        <table class="table">
            <thead>
            <tr>
                <th colspan="2">{{ \Lang::get('borrower.loan.partials.expected-payments') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($repaymentSchedule as $repaymentScheduleInstallment)
            <tr>
                <td>{{ $repaymentScheduleInstallment->getInstallment()->getDueDate()->format('M j, Y') }}</td>
                <td>{{ $repaymentScheduleInstallment->getInstallment()->getAmount() }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
