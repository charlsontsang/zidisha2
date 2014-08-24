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
            ['acceptedAt' => 'Bids Accepted Date', 'borrowerName' => 'Borrower Name'],
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

    @if(!$loans->isEmpty())
        <table class="table table-striped">
            <tbody>
                @foreach($loans as $loan)

                <?php
                    if ($loan->isAuthorized()) {
                        $principalAmount = $loan->getAuthorizedAmount();
                    } else {
                        $principalAmount = Zidisha\Currency\Converter::fromUSD($loan->getUsdAmount(), $currency, $exchangeRate);
                    }

                    $siftScienceScore = $loan->getSiftScienceScore();
                    $siftScienceProfile = "https://siftscience.com/console/users/" . $loan->getBorrowerId();
                ?>

                <tr>
                    <td>
                        <div class="row">
                            <div class="col-xs-12">
                                <h3><a href="{{ route('borrower:public-profile', $loan->getBorrower()->getId()) }}">{{ $loan->getBorrower()->getName() }}</a></h3>
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
                            {{ BootstrapForm::open(['action' => 'PendingDisbursementsController@post' . ($loan->isAuthorized() ? 'Disburse' : 'Authorize')]) }}

                            {{ BootstrapForm::hidden('loanId', $loan->getId()) }}

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
                                <dd>{{ $loan->getAcceptedAt('M j, Y') }}</dd>

                                @if($loan->isAuthorized())
                                <dt>Date Authorized</dt>
                                <dd>{{ $loan->getAuthorizedAt('M j, Y') }}</dd>
                                @endif
                                
                                <dt>Principal Amount</dt>
                                <dd>
                                    {{ BootstrapForm::text($loan->isAuthorized() ? 'disbursedAmount' : 'authorizedAmount', $principalAmount->getAmount(), ['label' => false, 'style' => 'width: 180px;']) }}
                                </dd>

                                @if(!$loan->isAuthorized())
                                <dt>Date Authorized</dt>
                                    <dd>
                                        {{ BootstrapForm::datepicker('authorizedAt', null, ['label' => false, 'style' => 'width: 180px;']) }}
                                        {{ BootstrapForm::submit('Confirm Authorization') }}
                                    </dd>
                                @endif
                                
                                @if($loan->isAuthorized())   
                                    @if ($loan->getRegistrationFee()->isPositive())
                                        <dt>Registration Fee</dt>
                                        <dd>
                                            {{ BootstrapForm::text('registrationFee', $loan->getRegistrationFee()->getAmount(), ['label' => false, 'style' => 'width: 180px;']) }}
                                        </dd>
                                    @endif
                                        
                                    <dt>Date Disbursed</dt>
                                    <dd>
                                        {{ BootstrapForm::datepicker('disbursedAt', null, ['label' => false, 'style' => 'width: 180px;']) }}
                                        {{ BootstrapForm::submit('Confirm Disbursement') }}
                                    </dd>
                                @endif
                             </dl>
                            {{ BootstrapForm::close() }}

                            <!-- Loan Notes -->
                           <hr/>
                            <strong>Notes</strong>
                            <br/>
                            @if(isset($loanNotes[$loan->getId()]))
                                <ul>
                                    @foreach($loanNotes[$loan->getId()] as $loanNote)
                                        <li>
                                            <span class="text-muted">
                                                {{ $loanNote->getCreatedAt('M j, Y') }} by {{ $loanNote->getUser()->getUserName() }}
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
    @else
        <p>
            No pending disbursements.
        </p>
    @endif


@stop
