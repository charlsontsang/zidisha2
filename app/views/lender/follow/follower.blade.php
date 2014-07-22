<?php
    $followByDefault = isset($followByDefault) ? $followByDefault : false;
    $following = $followByDefault;
    $notifyComment = true;
    $notifyLoanApplication = true;
    if ($follower) {
        $following = $follower->getActive();
        $notifyComment = $follower->getNotifyComment();
        $notifyLoanApplication = $follower->getNotifyLoanApplication();
    } else {
        $notifyComment = $lender->getPreferences()->getNotifyComment();
        $notifyLoanApplication = $lender->getPreferences()->getNotifyLoanApplication();
    }
?>
{{ BootstrapForm::open(['route' => [$following ? 'lender:unfollow' : 'lender:follow', $loan->getBorrowerId()]]) }}
    <div class="row">
        <div class="col-xs-2">
            {{ BootstrapForm::submit($following ? 'Unfollow' : 'Follow') }}
        </div>
        <div class="col-xs-10">
            <div class="follow-notifications" {{ $following ? '' : 'style="display:none"' }}>
                <p class="omega">Notify me when {{ $borrower->getName() }}:</p>
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
                <span class="text-success" style="display:none;padding-left:12px;">Your preference has been saved.</span>
            </div>
        </div>
    </div>
{{ BootstrapForm::close() }}
