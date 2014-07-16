@extends('layouts.master')

@section('content')
<a href="{{ route('admin:pending-disbursements:select-country') }}">Back</a>
    @if($loans)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>National Id</th>
                    <th>Telephone</th>
                    <th>Special Instructions</th>
                    <th>Bids Accepted</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)

                <?php

                $loanAmount = Zidisha\Currency\Converter::fromUSD($loan->getAmount(), $currency, $exchangeRate);
                $serviceFee = Zidisha\Currency\Money::create($loan->getRegistrationFee(), $currency);
                $principalAmount = $loanAmount->subtract($serviceFee);

                ?>

                    <tr>
                        <td> {{ $loan->getBorrower()->getName() }}</td>
                        <td> {{ $loan->getBorrower()->getProfile()->getAddress() }} </td>
                        <td> {{ $loan->getBorrower()->getProfile()->getNationalIdNumber() }} </td>
                        <td> {{ $loan->getBorrower()->getProfile()->getPhoneNumber() }} </td>
                        <td> {{ $loan->getAcceptBidsNote() }} </td>
                        <td> {{ $loan->getAcceptedAt('D M, YY') }} </td>
                        <td> {{ $principalAmount }} </td>

                        <!-- Loan Status Column -->
                        <td>
                            @if(!$loan->isAuthorized())
                                Pending Authorization
                                <br/>
                                {{ BootstrapForm::open(array('action' => 'PendingDisbursementsController@postAuthorize', 'translationDomain' => 'admin.reports.pending-disbursements.date-authorized')) }}
                                    {{ BootstrapForm::text('authorizedAt') }}
                                    {{ BootstrapForm::hidden('loanId', $loan->getId()) }}
                                    {{ BootstrapForm::submit('Confirm Authorization') }}
                                {{ BootstrapForm::close() }}

                            @else
                                Authorized
                                <br/>
                                Date : {{ $loan->getAuthorizedAt('M D, Y') }}

                                {{ BootstrapForm::open(array('action' => 'PendingDisbursementsController@postDisburse', 'translationDomain' => 'admin.reports.pending-disbursements.loan-notes')) }}
                                    {{ BootstrapForm::text('disbursedDate') }}
                                    {{ BootstrapForm::hidden('loanId', $loan->getId()) }}
                                    {{ BootstrapForm::hidden('principalAmount', $principalAmount->getAmount()) }}
                                    {{ BootstrapForm::submit('Confirm Disbursement') }}
                                {{ BootstrapForm::close() }}
                            @endif
                        </td>

                        <!-- Loan Notes Column -->
                        <td>
                            @if($loanNotes = $loan->getLoanNotes())
                                <ul>
                                    @foreach($loanNotes as $loanNote)
                                        <li>
                                            <p> {{ $loanNote->getNote() }} </p>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{ BootstrapForm::open(array('action' => 'PendingDisbursementsController@postLoanNote', 'translationDomain' => 'admin.reports.pending-disbursements.loan-notes')) }}
                                {{ BootstrapForm::textarea('note') }}
                                {{ BootstrapForm::hidden('loanId', $loan->getId()) }}
                                {{ BootstrapForm::submit('Submit') }}
                            {{ BootstrapForm::close() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    {{ BootstrapHtml::paginator($loans)->links() }}
    @endif


@stop
