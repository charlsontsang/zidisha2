@extends('layouts.side-menu')

@section('page-title')
Your Invites
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
<p>
    {{ \Lang::get('borrower.invite.invites-message', ['minRepaymentRate' => $minRepaymentRate, 'borrowerInviteCredit' => $borrowerInviteCredit]) }}
</p>
<p>
    {{ \Lang::get('borrower.invite.success-rate') }}:
    <i class="fa fa-info-circle successRate" data-toggle="tooltip" data-placement="bottom" title="{{ \Lang::get('borrower.invite.success-rate-tooltip') }}"></i>
    &nbsp;&nbsp;&nbsp;<strong>{{ $successRate }}%</strong>
</p>
<p>
    {{ \Lang::get('borrower.invite.bonus-earned') }}:
    <i class="fa fa-info-circle successRate" data-toggle="tooltip" data-placement="bottom" title="{{ \Lang::get('borrower.invite.bonus-earned-tooltip') }}"></i>
    &nbsp;&nbsp;&nbsp;<strong>{{ $bonusEarned }}</strong>
</p>

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
               <?php $bonus = \Zidisha\Currency\Money::create(0, $currencyCode); ?>
            @else
                <?php $lastLoan = \Zidisha\Loan\LoanQuery::create()->findLastLoan($invite->getInvitee()) ?>
                {{-- //TODO endrosement --}}
                @if(empty($lastLoan))
                    <?php $flag=1; ?>
                    <?php $name = $invite->getInvitee()->getName(); ?>
                    @if(!empty($invite->getInvitee()->getUser()->getFacebookId()))
                        @if(false)
                            <?php $flag=0; ?>
                        @endif
                    @endif
                    <?php $volunteerMentorStatus = $invite->getInvitee()->getActivationStatus() ?>
                    {{-- $BorrowerReports = //TODO; --}}
                    <?php $borrowerGuest = \Zidisha\Borrower\BorrowerGuestQuery::create()->filterByEmail($invite->getEmail())->findOne(); ?>
                    @if($borrowerGuest && $flag == 1)
                        <?php $status =  \Lang::get('borrower.invite.application-not-submitted'); ?>
                    @elseif($volunteerMentorStatus == \Zidisha\Borrower\Borrower::ACTIVATION_REVIEWED)
                        <?php $status =  \Lang::get('borrower.invite.application-pending-verification'); ?>
                    @elseif($volunteerMentorStatus == \Zidisha\Borrower\Borrower::ACTIVATION_APPROVED )
                        {{-- //TODO --}}
                        {{-- //TODO one more elseif --}}
                    @elseif( $volunteerMentorStatus == \Zidisha\Borrower\Borrower::ACTIVATION_DECLINED)
                        <?php $status =  \Lang::get('borrower.invite.application-decline'); ?>
                    @elseif($volunteerMentorStatus == \Zidisha\Borrower\Borrower::ACTIVATION_PENDING)
                        <?php $status =  \Lang::get('borrower.invite.application-pending-review'); ?>
                    @else
                        <?php $status =  \Lang::get('borrower.invite.no-loan'); ?>
                    @endif
                    <?php $repaymentRate = ''; ?>
                    <?php $bonus = \Zidisha\Currency\Money::create(0, $currencyCode); ?>
                @else
                    <?php $loanStatus = $invite->getInvitee()->getLoanStatus() ?>
                    <?php $repaymentSchedule = $repaymentService->getRepaymentSchedule($invite->getInvitee()->getActiveLoan()); ?>
                    <?php $id = $invite->getInvitee()->getActiveLoan()->getId(); ?>
                    <?php $name = "<a href='route('loan:index', $id)'>".$invite->getInvitee()->getName()."</a>"; ?>
                    @if($loanStatus == \Zidisha\Loan\Loan::OPEN)
                        <?php $status =  \Lang::get('borrower.invite.fundRaising-loan'); ?>
                    @elseif($repaymentSchedule->getMissedInstallmentCount() == 0 && $loanStatus = \Zidisha\Loan\Loan::ACTIVE)
                        <?php $status =  \Lang::get('borrower.invite.repaying-on-time'); ?>
                    @elseif($repaymentSchedule->getMissedInstallmentCount() != 0 && $loanStatus = \Zidisha\Loan\Loan::ACTIVE)
                        <?php $status =  \Lang::get('borrower.invite.past-due'); ?>
                    @endif
                    <?php $rate = $loanService->getOnTimeRepaymentScore($invite->getInvitee()); ?>
                    <?php $repaymentRate = number_format($rate)."%"; ?>
                    @if($rate >= $minRepaymentRate)
                        <?php $bonus = $borrowerInviteCredit; ?>
                    @else
                        <?php $bonus = \Zidisha\Currency\Money::create(0, $currencyCode); ?>
                    @endif
                @endif
            @endif
            <td data-title="Name">{{ $name }}</td>
            <td data-title="Email"><a href="#">{{ $invite->getEmail() }}</a></td>
            <td data-title="Status">{{ $status }}</td>
            <td data-title="RepaymentRate">{{ $repaymentRate }}</td>
            <td data-title="BounsCredit">
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
