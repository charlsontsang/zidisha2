<table class="table table-2-col">
    <tbody>
    <tr>
        <td>
            <strong>@lang('borrower.loan.requested-amount'):</strong>
        </td>
        <td>
            {{ $loan->getAmount() }}
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.repayment-period'):</strong>
        </td>
        <td>
            {{ $loan->getPeriod() }}
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.final-lender-interest-rate'):</strong>
        </td>
        <td>
            {{ $loan->getLenderInterestRate() }}%
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.service-fee-rate'):</strong>
        </td>
        <td>
            {{ $loan->getServiceFeeRate() }}%
        </td>
    </tr>

    @if($loan->getRegistrationFee()->isPositive())
    <tr>
        <td>
            <strong>@lang('borrower.loan.registration-fee'):</strong>
        </td>
        <td>
            {{ $loan->getRegistrationFee() }}
        </td>
    </tr>
    @endif

    <tr>
        <td>
            <strong>@lang('borrower.loan.total-interest-and-fees'):</strong>
        </td>
        <td>
            {{ $installmentCalculator->totalInterest()->round(2) }}
            ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.loan.weekly-interest-rate' : 'borrower.loan.monthly-interest-rate', [
                'interestRate' => $loan->getLenderInterestRate() + $loan->getServiceFeeRate(),
                'period' => $loan->getPeriod(),
            ]) }})
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.total-amount'):</strong>
        </td>
        <td>
            {{ $installmentCalculator->totalAmount()->round(2) }}
        </td>
    </tr>
    </tbody>
</table>
