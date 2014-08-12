@extends('layouts.master')

@section('page-title')
Invites
@stop

@section('content')
    <div class="page-header">
            {{ \Lang::get('borrower.invite.invites-message', ['minRepaymentRate' => $minRepaymentRate, 'currencyCode' => $currencyCode]) }}
    </div>
    <div>
        {{ \Lang::get('borrower.invite.success-rate') }}
        <i class="fa fa-info-circle successRate" data-toggle="tooltip" data-placement="bottom" title="{{ \Lang::get('borrower.invite.success-rate-tooltip') }}"></i>
        {{ $successRate }}%
    </div>
    <div>
        {{ \Lang::get('borrower.invite.bonus-earned') }}
        <i class="fa fa-info-circle successRate" data-toggle="tooltip" data-placement="bottom" title="{{ \Lang::get('borrower.invite.bonus-earned-tooltip') }}"></i>
        {{ $bonusEarned }}
    </div>

<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th>
            {{ \Lang::get('borrower.invite.name') }}
        </th>
        <th>
            {{ \Lang::get('borrower.invite.email') }}
        </th>
        <th>
            {{ \Lang::get('borrower.invite.status') }}
        </th>
        <th>
            {{ \Lang::get('borrower.invite.repayment-rate') }}
        </th>
        <th>
            {{ \Lang::get('borrower.invite.bonus-credit') }}
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $invite)
    <tr>
        @if(!$invite->getInviteeId())
           <?php $name = ''; ?>
           <?php $status = \Lang::get('borrower.invite.not-accepted'); ?>
           <?php $repaymentRate = ''; ?>
           <?php $bonus = 0; ?>
        @else
            <?php $lastLoan = LoanQuery::create()->getLastLoan($invite->getInvitee()) ?>
            {{-- //TODO endrosement --}}
            @if(empty($lastLoan))
                <?php $flag=1; ?>
                <?php $name = $invite->getInvitee()->getName(); ?>
                @if(!empty($invite->getInvitee()->getUser()->getFacebookId()))
                    @if(false)
                        <?php $flag=0; ?>
                    @endif
                @endif
                <?php $volunteerMentor = $invite->getInvitee()->getVolunteerMentor() ?>
                {{-- $BorrowerReports = //TODO; --}}
                <?php $borrowerGuest = BorrowerGuestQuery::create()->filterByEmail($invite->getEmail())->findOne(); ?>
                @if($borrowerGuest && $flag == 1)
                    <?php $status =  \Lang::get('borrower.invite.application-not-submitted'); ?>
                @elseif($volunteerMentor->getStatus() == \Zidisha\Borrower\VolunteerMentor::STATUS_PENDING_VERIFICATION)
                    <?php $status =  \Lang::get('borrower.invite.application-pending-verification'); ?>
                @elseif($volunteerMentor->getStatus() == \Zidisha\Borrower\VolunteerMentor::STATUS_ASSIGNED_TO && $volunteerMentor)
                    {{-- //TODO --}}
                    {{-- //TODO one more elseif --}}
                @elseif($volunteerMentor->getStatus() == \Zidisha\Borrower\VolunteerMentor::STATUS_DECLINED)
                    <?php $status =  \Lang::get('borrower.invite.application-decline'); ?>
                @elseif($volunteerMentor->getStatus() == \Zidisha\Borrower\VolunteerMentor::STATUS_PENDING_REVIEW)
                    <?php $status =  \Lang::get('borrower.invite.application-pending-review'); ?>
                @else
                    <?php $status =  \Lang::get('borrower.invite.no-loan'); ?>
                @endif
                <?php $repaymentRate = ''; ?>
                <?php $bonus = 0; ?>
            @else
                <?php $loanStatus = $invite->getInvitee()->getLoanStatus() ?>
                <?php $repaymentSchedule = $repaymentService->getRepaymentSchedule($invite->getInvitee()->getActiveLoan()); ?>
                <?php $name = "<a href='route('\loan:index', $invite->getInvitee()->getActiveLoan()->getId()))'>".$invite->getInvitee()->getName()."</a>"; ?>
                @if($loanStatus == \Zidisha\Loan::OPEN)
                    <?php $status =  \Lang::get('borrower.invite.fundRaising-loan'); ?>
                @elseif($repaymentSchedule->getMissedInstallmentCount() == 0 && $loanStatus = \Zidisha\Loan::ACTIVE)
                    <?php $status =  \Lang::get('borrower.invite.repaying-on-time'); ?>
                @elseif($repaymentSchedule->getMissedInstallmentCount() != 0 && $loanStatus = \Zidisha\Loan::ACTIVE)
                    <?php $status =  \Lang::get('borrower.invite.past-due'); ?>
                @endif
                <?php $rate = $loanService->getOnTimeRepaymentScore($invite->getInvitee()); ?>
                <?php $repaymentRate = number_format($rate)."%"; ?>
                @if($rate >= minRepaymentRate)
                    {{-- $bonus = //TODO --}}
                @else
                    <?php $bonus = 0; ?>
                @endif
            @endif
        @endif
        <td data-title="Name">{{ $name }}</td>
        <td data-title="Email"><a href="#">{{ $invite->getEmail() }}</a></td>
        <td data-title="Status">{{ $status }}</td>
        <td data-title="RepaymentRate">{{ $repaymentRate }}</td>
        <td data-title="BounsCredit">
            {{ $currencyCode }}
            {{ $bonus }}
        </td>
        <td><a class="btn btn-primary"  href="{{ route('borrower:invites', $invite->getId()) }}">Remove</a></td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $('.successRate').tooltip()
</script>
@stop
