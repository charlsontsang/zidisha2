<?php
    $followByDefault = isset($followByDefault) ? $followByDefault : false;
    /** @var Zidisha\Lender\Follower $follower */
    $follower = isset($follower) ? $follower : false;
    $following = $followByDefault;
    $notifyComment = true;
    $notifyLoanApplication = true;
    if ($follower) {
        $following = true;
        $notifyComment = $follower->getNotifyComment();
        $notifyLoanApplication = $follower->getNotifyLoanApplication();
    } else {
        /** @var Zidisha\Lender\Lender $lender */
        $notifyComment = $lender->getPreferences()->getNotifyComment();
        $notifyLoanApplication = $lender->getPreferences()->getNotifyLoanApplication();
    }
?>
<div class="follow-settings" style="{{ $following ? '' : 'display:none' }}">
    <p>
        <a
            data-follow="unfollow"
            data-follow-enabled="{{ $followByDefault ? '' : 'enabled' }}"
            class="btn btn-default btn-block"
            href="{{ route('lender:unfollow', $loan->getBorrowerId()) }}"
            >
            Unfollow {{ $borrower->getFirstName() }}
        </a>
    </p>
    
    <div class="follow-notifications">
        <p class="omega">Notify me when {{ $borrower->getFirstName() }}:</p>
        {{ BootstrapForm::checkbox('notifyComment', true, $notifyComment, [
            'id' => 'notify-comment-' . $borrower->getId(),
            'label' => 'posts a new comment',
            'target' => route('lender:update-follower', $loan->getBorrowerId()),
        ]) }}
        {{ BootstrapForm::checkbox('notifyLoanApplication', true, $notifyLoanApplication, [
            'id' => 'notify-comment-' . $borrower->getId(),
            'label' => 'posts a new loan application',
            'target' => route('lender:update-follower', $loan->getBorrowerId())
        ]) }}
    </div>
</div>
