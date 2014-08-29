@extends('layouts.master')

@section('page-title')
    Reschedule loan
@stop

@section('content')
<div class="page-header">
    <h1>
        Reschedule Loan
    </h1>
</div>

<p>
    @lang('borrower.your-loans.reschedule.description')
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
            </tbody>
        </table>
    </div>
</div>

@include('partials/repayment-schedule-table', compact('repaymentSchedule'))

<hr/>

{{ BootstrapForm::open([
    'route' => ['borrower:post-reschedule-loan'],
    'translationDomain' => 'borrower.your-loans.reschedule'
]) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('installmentAmount', null, [
    'description' => Lang::get('borrower.your-loans.reschedule.installment-amount-description', [
                        'minInstallmentAmount' => $form->getMinInstallmentAmount(),
                        'maxPeriod'            => Setting::get('loan.maxExtraPeriodRescheduledLoan'),
                     ]),
    'prepend'     => $loan->getCurrencyCode(),
]) }}
{{ BootstrapForm::textarea('reason', null, [
    'description' => Lang::get('borrower.your-loans.reschedule.reason-description'),
    'rows'        => 5,
]) }}

<p>
    @lang('borrower.your-loans.reschedule.note')
</p>

{{ BootstrapForm::submit('submit') }}

{{ BootstrapForm::close() }}

@stop
