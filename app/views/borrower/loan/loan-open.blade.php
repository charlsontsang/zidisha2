@extends('borrower.loan.loan-base')

@section('content')
@parent

<div class="row">
    <div class="col-sm-6">
        @if($loan->isFullyFunded())
        <h2>Accept bids</h2>

        <div class="alert alert-warning" role="alert">
            @lang('borrower.your-loans.accept-bids.instructions')
        </div>

        <table class="table table-2-col">
            <tbody>
            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.requested-amount'):</strong>
                </td>
                <td>
                    {{ $loan->getAmount() }}
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
                    <strong>@lang('borrower.your-loans.final-lender-interest-rate'):</strong>
                </td>
                <td>
                    {{ $loan->getLenderInterestRate() }}%
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.service-fee-rate'):</strong>
                </td>
                <td>
                    {{ $loan->getServiceFeeRate() }}%
                </td>
            </tr>

            @if($loan->getRegistrationFee()->isPositive())
            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.registration-fee'):</strong>
                </td>
                <td>
                    {{ $loan->getRegistrationFee() }}
                </td>
            </tr>
            @endif

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.total-interest-and-fees'):</strong>
                </td>
                <td>
                    {{ $calculator->totalInterest()->round(2) }}
                    ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.your-loans.interest-rate-for-weeks' : 'borrower.your-loans.interest-rate-for-months', [
                    'interestRate' => $loan->getLenderInterestRate() + $loan->getServiceFeeRate(),
                    'period' => $loan->getPeriod(),
                    ]) }})
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.total-amount'):</strong>
                </td>
                <td>
                    {{ $calculator->totalAmount()->round(2) }}
                </td>
            </tr>
            </tbody>
        </table>

        <p>
            @lang('borrower.your-loans.accept-bids.schedule')
        </p>


        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>{{ \Lang::get('borrower.loan-application.publish-loan.table.due-date') }}</th>
                <th>{{ \Lang::get('borrower.loan-application.publish-loan.table.repayment-due', ['currencyCode' => $loan->getCurrencyCode()]) }}</th>
                <th>{{ \Lang::get('borrower.loan-application.publish-loan.table.balance-remaining') }}</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            $remainingAmount = $calculator->totalAmount();
            ?>
            @foreach($installments as $installment)
            <tr>
                <td>{{ $i }}</td>
                <?php $remainingAmount = $remainingAmount->subtract($installment->getAmount()) ?>
                <td>{{ $installment->getAmount()->round(2)->getAmount() }}</td>
                <td>{{ $remainingAmount->round(2)->getAmount() }}</td>
                <?php $i++; ?>
            </tr>
            @endforeach

            <tr>
                <td> <strong>{{ \Lang::get('borrower.loan-application.publish-loan.table.total-repayment') }}</strong> </td>
                <td> <strong> {{  $calculator->totalAmount()->round(2)->getAmount() }} </strong> </td>
                <td></td>
            </tr>
            </tbody>
        </table>

        {{ BootstrapForm::open([
        'action' => ['BorrowerLoanController@postAcceptBids', $loan->getId()],
        'translationDomain' => 'borrower.your-loans.accept-bids'
        ]) }}

        {{ BootstrapForm::textarea('acceptBidsNote', null, [
        'label' => false,
        'description' => $borrower->getCountry()->getAcceptBidsNote() ?: Lang::get('borrower.your-loans.accept-bids.default-note'),
        'rows' => 5,
        ]) }}

        {{ BootstrapForm::submit('submit') }}

        {{ BootstrapForm::close() }}
        @else
        <h2>Loan details</h2>
        
        <table class="table table-2-col">
            <tbody>
            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.requested-amount'):</strong>
                </td>
                <td>
                    {{ $loan->getAmount() }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.service-fee-rate'):</strong>
                </td>
                <td>
                    {{ $loan->getServiceFeeRate() }}%
                </td>
            </tr>

            @if($loan->getRegistrationFee()->isPositive())
            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.registration-fee'):</strong>
                </td>
                <td>
                    {{ $loan->getRegistrationFee() }}
                </td>
            </tr>
            @endif

            <tr>
                <td>
                    <strong>@lang('borrower.your-loans.expires-at'):</strong>
                </td>
                <td>
                    {{ $loan->getExpiresAt()->format('M j, Y') }}
                </td>
            </tr>
            </tbody>
        </table>
        @endif
    </div>
    <div class="col-sm-6">
        @if($bids)
        <h2>Loan Bids</h2>

        <table class="table">
            <thead>
            <tr>
                <th>Lender</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <!-- TODO accepted amounts? -->
            @foreach($bids as $bid)
            <tr>
                <td>{{ $bid->getLender()->getName() }}</td>
                <td>{{ $bid->getBidAmount() }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif

    </div>
</div>

@stop
