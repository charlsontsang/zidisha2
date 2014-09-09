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

    @if(isset($installmentAmount))
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
    @endif

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
            $loan->isWeeklyInstallment() ? 'borrower.loan.weekly-interest-rate' : 'borrower.loan.monthly-interest-rate',
            ['interestRate' => $maxInterestRate, 'period' => $period]
            ))
        </td>
    </tr>

    <tr>
        <td>
            <strong>@lang('borrower.loan.total-amount-due'):</strong>
        </td>
        <td>
            {{ $totalAmount }}
        </td>
    </tr>
    </tbody>
</table>
