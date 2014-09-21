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
<p>
    @lang('borrower.loan.reschedule.description')
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
            </tbody>
        </table>
    </div>
</div>

@include('partials/repayment-schedule-table', compact('repaymentSchedule'))

<hr/>

{{ BootstrapForm::open([
    'route' => ['borrower:post-reschedule-loan'],
    'translationDomain' => 'borrower.loan.reschedule'
]) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::text('installmentAmount', null, [
    'description' => Lang::get('borrower.loan.reschedule.installment-amount-description', [
                        'minInstallmentAmount' => $form->getMinInstallmentAmount(),
                        'maxPeriod'            => Setting::get('loan.maxExtraPeriodRescheduledLoan'),
                     ]),
    'prepend'     => $loan->getCurrencyCode(),
]) }}
{{ BootstrapForm::textarea('reason', null, [
    'description' => Lang::get('borrower.loan.reschedule.reason-description'),
    'rows'        => 5,
]) }}

<p>
    @lang('borrower.loan.reschedule.note')
</p>

{{ BootstrapForm::submit('submit') }}

{{ BootstrapForm::close() }}

@stop
