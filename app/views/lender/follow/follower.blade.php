<?php
    $enableFollow = isset($enableFollow) ? $enableFollow : false;
    $enableUnfollow = true;
    /** @var Zidisha\Lender\Follower $follower */
    $follower = isset($follower) ? $follower : false;
    $following = $enableFollow;
    $notifyComment = true;
    $notifyLoanApplication = true;
    if ($follower) {
        $following = true;
        $enableUnfollow = !$follower->isFunded();
        $notifyComment = $follower->getNotifyComment();
        $notifyLoanApplication = $follower->getNotifyLoanApplication();
    } else {
        /** @var Zidisha\Lender\Lender $lender */
        $notifyComment = $lender->getPreferences()->getNotifyComment();
        $notifyLoanApplication = $lender->getPreferences()->getNotifyLoanApplication();
    }
?>
<div class="follow-settings" style="{{ $following ? '' : 'display:none' }}">
    <p style="{{ $enableUnfollow ? '' : 'display:none' }}">
        <a
            data-follow="unfollow"
            data-follow-enabled="{{ $enableFollow ? 'enabled' : '' }}"
            class="btn btn-default"
            href="{{ route('lender:unfollow', $borrower->getId()) }}"
            >
            Unfollow {{ $borrower->getFirstName() }}
        </a>
    </p>
    
    <div class="follow-notifications">
        <p class="omega">Notify me when {{ $borrower->getFirstName() }}:</p>
        {{ BootstrapForm::checkbox('notifyComment', true, $notifyComment, [
            'id' => 'notify-comment-' . $borrower->getId(),
            'label' => 'posts a new comment',
            'target' => route('lender:update-follower', $borrower->getId()),
        ]) }}
        {{ BootstrapForm::checkbox('notifyLoanApplication', true, $notifyLoanApplication, [
            'id' => 'notify-comment-' . $borrower->getId(),
            'label' => 'posts a new loan application',
            'target' => route('lender:update-follower', $borrower->getId())
        ]) }}
    </div>
</div>
