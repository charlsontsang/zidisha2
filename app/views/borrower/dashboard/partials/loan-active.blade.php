@include('borrower.loan.next-installment', compact('repaymentSchedule'))

<p>
    <a href="{{ route('borrower:loan') }}" class="btn btn-primary">
        @lang('borrower.dashboard.loan-page')
    </a>
</p>
