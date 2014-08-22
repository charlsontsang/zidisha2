@extends('layouts.master')

@section('content')
    @if($loan->isOpen())
        <div>
            <h1>Loan Open</h1>
            <a href="{{ action('LoanController@getIndex', [ 'loanId' => $loan->getId() ]) }}" class="btn btn-primary">
                View loan profile page
            </a>
            
            <div class="row">
                <div class="col-sm-6">
                    @if($loan->isFullyFunded())
                    <h2>Accept bids</h2>

                    <div class="alert alert-warning" role="alert">
                        @lang('borrower.your-loans.accept-bids.instructions')
                    </div>

                    <style>
                        .table.table-2-col td {
                            width: 50%;
                        }
                    </style>
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
                                    {{ $loan->getInstallmentCount() }}
                                </td>
                            </tr>
    
                            <tr>
                                <td>
                                    <strong>@lang('borrower.your-loans.final-lender-interest-rate'):</strong>
                                </td>
                                <td>
                                    5%
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
    
                            <!--  TODO first loan -->
                            @if(true)
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
                                    {{ $calculator->totalInterest() }}
                                    ({{ Lang::get($loan->isWeeklyInstallment() ? 'borrower.your-loans.interest-rate-for-weeks' : 'borrower.your-loans.interest-rate-for-months', [
                                        'interestRate' => 16,
                                        'period' => $loan->getInstallmentCount(),
                                    ]) }})
                                </td>
                            </tr>
    
                            <tr>
                                <td>
                                    <strong>@lang('borrower.your-loans.total-amount'):</strong>
                                </td>
                                <td>
                                    {{ $calculator->totalAmount() }}
                                </td>
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
        </div>
    @endif
@stop
