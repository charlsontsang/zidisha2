@extends('layouts.master')

@section('page-title')
    @lang('borrower.your-loans.reschedule.title')
@stop

@section('content')
<div class="page-header">
    <h1>
        @lang('borrower.your-loans.reschedule.title')
    </h1>
</div>

<p class="alert alert-warning">
    @lang('borrower.your-loans.reschedule.confirmation-note', [
        'cancel' => Lang::get('borrower.your-loans.reschedule.cancel'),
        'confirm' => Lang::get('borrower.your-loans.reschedule.confirm'),
    ])
</p>

<div class="row">
    <div class="col-md-8">
        <table class="table table-2-col table-borderless table-condensed">
            <tbody>
            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.disbursed-amount'):</strong>
                </td>
                <td>
                    {{ $loan->getDisbursedAmount() }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <h4>@lang('borrower.your-loans.reschedule.current-schedule')</h4>
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.repayment-period'):</strong>
                </td>
                <td>
                    {{ $loan->getPeriod() }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.total-interest-and-fees'):</strong>
                </td>
                <td>
                    {{ $loan->getTotalInterest()->round(2) }}
                    ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.your-loans.interest-rate-for-weeks' : 'borrower.your-loans.interest-rate-for-months', [
                        'interestRate' => $loan->getTotalInterestRate(),
                        'period' => $loan->getPeriod(),
                    ]) }})
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.total-amount-due'):</strong>
                </td>
                <td>
                    {{ $loan->getTotalAmount()->round(2) }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <h4>@lang('borrower.your-loans.reschedule.new-schedule')</h4>
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.repayment-period'):</strong>
                </td>
                <td>
                    {{ $repaymentSchedule->getPeriod() }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.total-interest-and-fees'):</strong>
                </td>
                <td>
                    {{ $repaymentSchedule->getTotalInterest()->round(2) }}
                    ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.your-loans.interest-rate-for-weeks' : 'borrower.your-loans.interest-rate-for-months', [
                        'interestRate' => $loan->getTotalInterestRate(),
                        'period' => $loan->getPeriod(),
                    ]) }})
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.total-amount-due'):</strong>
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
    'translationDomain' => 'borrower.your-loans.reschedule'
]) }}

<div class="row">
    <div class="col-xs-6">
        <a href="{{ route('borrower:reschedule-loan') }}" class="btn btn-default">@lang('borrower.your-loans.reschedule.cancel')</a>
    </div>
    <div class="col-xs-6">
        {{ BootstrapForm::submit('confirm', ['class' => 'btn btn-success']) }}
    </div>
</div>

{{ BootstrapForm::close() }}

@stop
