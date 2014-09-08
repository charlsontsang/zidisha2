<table class="table table-2-col">
    <tbody>
    <tr>
        <td>
            <strong>@lang('borrower.loan.requested-amount'):</strong>
        </td>
        <td>
            {{ $amount }}
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.maximum-interest-rate'):</strong>
        </td>
        <td>
            {{ $maxInterestRate }} %
        </td>
    </tr>

    <tr>
        <td>
            <strong>
                @if($loan->isWeeklyInstallment())
                @lang('borrower.loan.weekly-repayment-amount'):
                @else
                @lang('borrower.loan.monthly-repayment-amount'):
                @endif
            </strong>
        </td>
        <td>
            {{ $installmentAmount }}
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.repayment-period'):</strong>
        </td>
        <td>
            {{ $period }}
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.maximum-interest-and-transaction-fees'):</strong>
        </td>
        <td>
            {{ $totalInterest }}
            (@lang(
            $loan->isWeeklyInstallment() ? 'borrower.loan-application.publish.weekly-interest-rate' : 'borrower.loan-application.publish.monthly-interest-rate',
            ['interestRate' => $maxInterestRate, 'period' => $period]
            ))
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.total-amount-due-date'):</strong>
        </td>
        <td>
            {{ $totalAmount }}
        </td>
    </tr>
    </tbody>
</table>
