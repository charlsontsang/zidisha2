@extends('layouts.master')

@section('content')
<div class="page-header">
    <h1>
        Pending Disbursements <small>{{ $country->getName() }}</small>
    </h1>
</div>

<div class="row">
    <div class="col-sm-6">
        {{ BootstrapForm::open(['action' => ['PendingDisbursementsController@postPendingDisbursements'], 'class' => 'form-inline']) }}

        {{ BootstrapForm::select('countryCode', $countries->toKeyValue('countryCode', 'name'), $country->getCountryCode(), ['label' => false]) }}

        {{ BootstrapForm::submit('Select') }}

        {{ BootstrapForm::close() }}
        <br/>
    </div>
    <div class="col-sm-6">
        {{ BootstrapForm::open([
            'action' => ['PendingDisbursementsController@getPendingDisbursements', $country->getCountryCode()],
            'method' => 'get',
            'class' => 'form-inline',
        ]) }}

        {{ BootstrapForm::select(
            'orderBy',
            ['borrowerName' => 'Borrower Name', 'acceptedAt' => 'Bids Accepted Date'],
            $orderBy,
            ['label' => false]
        ) }}
        {{ BootstrapForm::select(
            'orderDirection',
            ['asc' => 'ascending', 'desc' => 'descending'],
            $orderDirection,
            ['label' => false]
        ) }}

        {{ BootstrapForm::submit('Sort') }}

        {{ BootstrapForm::close() }}
        <br/>
    </div>
</div>

    @if($loans)
        <table class="table table-striped">
            <tbody>
                @foreach($loans as $loan)

                <?php

                $loanAmount = Zidisha\Currency\Converter::fromUSD($loan->getUsdAmount(), $currency, $exchangeRate);
                $registrationFee = Zidisha\Currency\Money::create($loan->getRegistrationFee(), $currency);
                $principalAmount = $loanAmount->subtract($registrationFee);

                ?>

                <tr>
                    <td>
                        <div class="row">
                            <div class="col-xs-12">
                                <h3>{{ $loan->getBorrower()->getName() }}</h3>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-6">
                            <dl class="dl-horizontal dl-horizontal-left">                                
                                <dt>Phone Number</dt>
                                <dd>{{ $loan->getBorrower()->getProfile()->getPhoneNumber() }}</dd>
                                
                                <dt>Location</dt>
                                <dd>{{ $loan->getBorrower()->getProfile()->getAddress() }}</dd>
                                
                                <dt>National ID</dt>
                                <dd>{{ $loan->getBorrower()->getProfile()->getNationalIdNumber() }}</dd>

                                @if($loan->getAcceptBidsNote())
                                <dt>Special Instructions</dt>
                                <dd>{{ $loan->getAcceptBidsNote() }}</dd>
                                @endif
                            </dl>

                            <?php 
                                // TODO
                                $siftScienceScore = $loan->getSiftScienceScore() + 50;
                                $siftScienceProfile = "https://siftscience.com/console/users/" . $loan->getBorrowerId();
                            ?>


                            @if($siftScienceScore > 50 && $siftScienceScore <75)
                                <div class="alert alert-warning">
                                    This member has a Sift Score of {{ $siftScienceScore }}, indicating an unusually high level of risk.
                                    <br/><br/>

                                    Please conduct a telephone interview of the member and the community leader before disbursing this loan.
                                    <br/><br/>

                                    <a href="{{ $siftScienceProfile }}" target="_blank">View Sift Science profile</a>
                                </div>
                            @elseif($siftScienceScore >= 75)
                                <div class="alert alert-warning">
                                    This applicant has a Sift Score of {{ $siftScienceScore }}, indicating a very high level of risk.
                                    <br/><br/>

                                    Please consult the director before disbursing this loan.
                                    <br/><br/>

                                    <a href="{{ $siftScienceProfile }}" target="_blank">View Sift Science profile</a>
                                </div>
                            @endif

                        </div>
                        <div class="col-sm-6">
                            <dl class="dl-horizontal dl-horizontal-left">
                                <dt>Status</dt>
                                <dd>
                                    @if($loan->isAuthorized())
                                        <span class="label label-success">Authorized</span>
                                    @else
                                        <span class="label label-default">Pending Authorization</span>
                                    @endif
                                </dd>
                                
                                <dt>Date Bids Accepted</dt>
                                <dd>{{ $loan->getAcceptedAt('D M, Y') }}</dd>
                                
                                <dt>Requested Amount</dt>
                                <dd>{{ $principalAmount }}</dd>

                                <dt>Date Authorized</dt>
                                <dd>
                                    @if($loan->isAuthorized())
                                        {{ $loan->getAuthorizedAt('M D, Y') }}
                                    @else
                                        {{ BootstrapForm::open(array('action' => 'PendingDisbursementsController@postAuthorize')) }}
                                        {{ BootstrapForm::datepicker('authorizedAt', null, ['label' => false, 'style' => 'width: 180px;']) }}
                                        {{ BootstrapForm::hidden('loanId', $loan->getId()) }}
                                        {{ BootstrapForm::submit('Confirm Authorization') }}
                                        {{ BootstrapForm::close() }}
                                    @endif
                                </dd>

                                @if($loan->isAuthorized())
                                <dt>Disbursement Date</dt>
                                <dd>
                                    {{ BootstrapForm::open(array('action' => 'PendingDisbursementsController@postDisburse')) }}
                                    {{ BootstrapForm::datepicker('disbursedAt', null, ['label' => false, 'style' => 'width: 180px;']) }}
                                    {{ BootstrapForm::hidden('loanId', $loan->getId()) }}
                                    {{ BootstrapForm::hidden('principalAmount', $principalAmount->getAmount()) }}
                                    {{ BootstrapForm::submit('Confirm Disbursement') }}
                                    {{ BootstrapForm::close() }}
                                </dd>
                                @endif
                             </dl>

                            <!-- Loan Notes -->
                           <hr/>
                            <strong>Notes</strong>
                            <br/>
                            @if(isset($loanNotes[$loan->getId()]))
                                <ul>
                                    @foreach($loanNotes[$loan->getId()] as $loanNote)
                                        <li>
                                            <span class="text-muted">
                                                {{ $loanNote->getCreatedAt('M D, Y') }} by {{ $loanNote->getUser()->getUserName() }}
                                            </span>
                                            <p> {{ $loanNote->getNote() }} </p>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{ BootstrapForm::open(array('action' => 'PendingDisbursementsController@postLoanNote')) }}
                                {{ BootstrapForm::textarea('note', null, ['rows' => '5', 'label' => false]) }}
                                {{ BootstrapForm::hidden('loanId', $loan->getId()) }}
                                {{ BootstrapForm::submit('Submit') }}
                            {{ BootstrapForm::close() }}
                        </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    {{ BootstrapHtml::paginator($loans)->links() }}
    @endif


@stop
