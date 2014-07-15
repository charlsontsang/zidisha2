@extends('layouts.master')

@section('content')
    @if($loan->isOpen())
        <div class="row">
            <h1>Loan Open</h1>

            <h2>Loan Bids</h2>

            @if($bids)
                <ul>
                    @foreach($bids as $bid)
                        <li>
                            <b>Lender Name</b> = {{ $bid->getLender()->getFirstName() . ' ' . $bid->getLender()->getLastName()}}
                            <br/>
                            <b>Bid Amount</b> = {{ $bid->getBidAmount() }}
                            <br/>
                            <b>Interest Rate</b> = {{ $bid->getInterestRate() }}
                            <br/>
                            <b>Accepted Amount</b> = {{ $bid->getAcceptedAmount() }}
                        </li>
                            <br/>
                    @endforeach
                </ul>
            @endif

            <h2>Accept Bid notes</h2>
            <p> {{ $borrower->getCountry()->getAcceptBidsNote() }} </p>

            {{ BootstrapForm::open(['action' => ['BorrowerLoanController@postAcceptBids', $loan->getId()], 'translationDomain' => 'borrower.accept-bids']) }}

            {{ BootstrapForm::submit('accept-bids') }}

            {{ BootstrapForm::close() }}

            <br/>
        </div>
    @endif

    <div class="row">
        <a href="{{ action('LoanController@getIndex', [ 'loanId' => $loan->getId() ]) }}">
            Goto loan page
        </a>
    </div>
@stop
