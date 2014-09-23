@extends('layouts.master')

@section('page-title')
Pending Disbursements
@stop

@section('content')
<div class="page-header">
    <h1>
        Pending Disbursements
    </h1>
</div>

<div class="row">
    <div class="col-sm-5">
        {{ BootstrapForm::open(['action' => ['PendingDisbursementsController@postPendingDisbursements'], 'class' => 'form-inline']) }}

        {{ BootstrapForm::select('countryCode', $countries->toKeyValue('countryCode', 'name'), $country->getCountryCode(), ['label' => false, 'id' => 'country']) }}

        {{ BootstrapForm::close() }}
        <br/>
    </div>
</div>

    @if(!$loans->isEmpty())
        <table class="table table-striped" id="disbursements">
            <thead>
                <th>Borrower</th>
                <th>Bid Acceptance Date</th>
            </thead>
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

                <tr id="loan-id-{{ $loan->getId() }}">
                    <td>
                        <div class="row">
                            <div class="col-xs-12">
                                <h3>
                                    <a href="{{ route('admin:borrower', $loan->getBorrowerId()) }}">
                                        {{ $loan->getBorrower()->getName() }}
                                    </a>
                                </h3>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-6">
                            <dl class="dl-horizontal dl-horizontal-left">                                
                            <dt>Name</dt>
                            <dd>
                                <a href="{{ route('admin:borrower', $loan->getBorrowerId())}}">
                                    {{ $loan->getBorrower()->getName() }}
                                </a>
                            </dd>
                                                           
                            <dt>Phone Number</dt>
                            <dd>{{ BootstrapHtml::number($loan->getBorrower()->getProfile()->getPhoneNumber(), $loan->getBorrower()->getCountry()->getCountryCode()) }}</dd>

                            
                            <dt>Email</dt>
                            <dd>{{ $loan->getBorrower()->getUser()->getEmail() }}</dd>
                            
                            <dt>City</dt>
                            <dd>{{ $loan->getBorrower()->getProfile()->getCity() }}</dd>
                            
                            <dt>National ID</dt>
                            <dd>{{ $loan->getBorrower()->getProfile()->getNationalIdNumber() }}</dd>

                            @if($loan->getAcceptBidsNote())
                            <dt>Special Instructions</dt>
                            <dd>{{ $loan->getAcceptBidsNote() }}</dd>
                            @endif
                            <dt>Notes</dt>
                            <dd>
                                @if(isset($adminNotes[$loan->getId()]))
                                    <ul>
                                        @foreach($adminNotes[$loan->getId()] as $adminNote)
                                            <li>
                                                <span class="text-muted">
                                                    {{ $adminNote->getCreatedAt('M j, Y') }} by {{ $adminNote->getUser()->getUserName() }}
                                                </span>
                                                <p> {{ $adminNote->getNote() }} </p>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <a href="#" class="add-note-toggle">Add note</a>
                                
                                <div id="add-note" class="collapse">                                   
                                    {{ BootstrapForm::open(array('action' => 'PendingDisbursementsController@postLoanNote')) }}
                                        {{ BootstrapForm::textarea('note', null, ['rows' => '5', 'label' => false]) }}
                                        {{ BootstrapForm::hidden('loanId', $loan->getId()) }}
                                        {{ BootstrapForm::submit('Submit') }}
                                    {{ BootstrapForm::close() }}
                                </div>

                            </dd>
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
                        </div>
                    </td>
                    <td>
                        {{ BootstrapForm::open(['action' => ['PendingDisbursementsController@post' . ($loan->isAuthorized() ? 'Disburse' : 'Authorize'), $loan->getId()]]) }}

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
                                {{ BootstrapForm::text($loan->isAuthorized() ? 'disbursedAmount' : 'authorizedAmount', $principalAmount->getAmount(), [
                                    'label' => false,
                                    'style' => 'width: 180px;',
                                    'data-principal-amount' => '',
                                ]) }}
                            </dd>

                            @if ($loan->getRegistrationFee()->isPositive())
                                <dt>Registration Fee</dt>
                                <dd>
                                    @if($loan->isAuthorized())
                                        {{ BootstrapForm::text('registrationFee', $loan->getRegistrationFee()->getAmount(), ['label' => false, 'style' => 'width: 180px;']) }}
                                    @else
                                        {{ $loan->getRegistrationFee() }}
                                        {{ BootstrapForm::hidden('registrationFee', $loan->getRegistrationFee()->getAmount()) }}
                                    @endif
                                </dd>

                                <dt>Net Amount to Pay</dt>
                                <dd data-net-amount="">
                                    {{ $principalAmount->subtract($loan->getRegistrationFee()) }}
                                </dd>
                            @endif
                            
                            @if(!$loan->isAuthorized())
                                <dt>Date Authorized</dt>
                                <dd>
                                    {{ BootstrapForm::datepicker('authorizedAt', null, ['label' => false, 'style' => 'width: 180px;']) }}
                                    {{ BootstrapForm::submit('Confirm Authorization') }}
                                </dd>
                            @endif
                            
                            @if($loan->isAuthorized())   
                                <dt>Date Disbursed</dt>
                                <dd>
                                    {{ BootstrapForm::datepicker('disbursedAt', null, ['label' => false, 'style' => 'width: 180px;']) }}
                                    {{ BootstrapForm::submit('Confirm Disbursement') }}
                                </dd>
                            @endif
                         </dl>
                        {{ BootstrapForm::close() }}
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

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#disbursements').dataTable({
            'searching': true
        });
        $('.add-note-toggle').click(function () {
            $("#add-note").collapse('toggle');
            return false;
        });
    });
    $(function () {
        $('[name=registrationFee], [data-principal-amount]').on('keyup', function() {
            var $this = $(this),
                $principalAmount = $(this).closest('tr').find('[data-principal-amount]'),
                $registrationFee = $(this).closest('tr').find('[name=registrationFee]'),
                $netAmount = $(this).closest('tr').find('[data-net-amount]');
            
            $netAmount.text($principalAmount.val() - $registrationFee.val());
        });
    });
    $(function() {
        $('#country').change(function() {
            this.form.submit();
    });
});
</script>
@stop
