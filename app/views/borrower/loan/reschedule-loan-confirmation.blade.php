@extends('layouts.side-menu-simple')

@section('page-title')
    @lang('borrower.loan.reschedule.title')
@stop

@section('menu-title')
    @lang('borrower.menu.links-title')
@stop

@section('menu-links')
    @include('partials.nav-links.borrower-links')
@stop


@section('page-content')
<p class="alert alert-warning">
    @lang('borrower.loan.reschedule.confirmation-note', [
        'cancel' => Lang::get('borrower.loan.reschedule.cancel'),
        'confirm' => Lang::get('borrower.loan.reschedule.confirm'),
    ])
</p>

<div class="row">
    <div class="col-md-8">
        <table class="table table-2-col table-borderless table-condensed">
            <tbody>
            <tr>
                <td>
                    <strong>@lang('borrower.loan.disbursed-amount'):</strong>
                </td>
                <td>
                    {{ $loan->getDisbursedAmount() }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <h4>@lang('borrower.loan.reschedule.current-schedule')</h4>
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
                    <strong>@lang('borrower.loan.total-interest-and-fees'):</strong>
                </td>
                <td>
                    {{ $loan->getTotalInterest()->round(2) }}
                    ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.loan.interest-rate-for-weeks' : 'borrower.loan.interest-rate-for-months', [
                        'interestRate' => $loan->getTotalInterestRate(),
                        'period' => $loan->getPeriod(),
                    ]) }})
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.loan.total-amount-due'):</strong>
                </td>
                <td>
                    {{ $loan->getTotalAmount()->round(2) }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <h4>@lang('borrower.loan.reschedule.new-schedule')</h4>
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.loan.repayment-period'):</strong>
                </td>
                <td>
                    {{ $repaymentSchedule->getPeriod() }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.loan.total-interest-and-fees'):</strong>
                </td>
                <td>
                    {{ $repaymentSchedule->getTotalInterest()->round(2) }}
                    ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.loan.interest-rate-for-weeks' : 'borrower.loan.interest-rate-for-months', [
                        'interestRate' => $loan->getTotalInterestRate(),
                        'period' => $repaymentSchedule->getPeriod(),
                    ]) }})
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.loan.total-amount-due'):</strong>
                </td>
                <td>
                    {{ $repaymentSchedule->getTotalAmountDue()->round(2) }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

@include('partials/repayment-schedule-table', compact('repaymentSchedule'))

<hr/>

{{ BootstrapForm::open([
    'route' => ['borrower:post-reschedule-loan-confirmation'],
    'translationDomain' => 'borrower.loan.reschedule'
]) }}

<div class="row">
    <div class="col-xs-6">
        <a href="{{ route('borrower:reschedule-loan') }}" class="btn btn-default">@lang('borrower.loan.reschedule.cancel')</a>
    </div>
    <div class="col-xs-6">
        {{ BootstrapForm::submit('confirm', ['class' => 'btn btn-success']) }}
    </div>
</div>

{{ BootstrapForm::close() }}

@stop
