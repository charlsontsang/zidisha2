<?php
    $followByDefault = isset($followByDefault) ? $followByDefault : false;
    $following = $followByDefault;
    $notifyComment = true;
    $notifyLoanApplication = true;
    if ($follower) {
        $following = $follower->getActive();
        $notifyComment = $follower->getNotifyComment();
        $notifyLoanApplication = $follower->getNotifyNewLoan();
    } else {
        $notifyComment = $lender->getPreferences()->getNotifyComment();
        $notifyLoanApplication = $lender->getPreferences()->getNotifyLoanApplication();
    }
?>
<form method="post" action="updateprocess.php" class="follow-form">
    <input type='hidden' name='borrower_id' value="{{ $borrower->getId() }}"/>
    <table style="border:0">
        <tr>
            <td style="border:0;padding-left: 0;vertical-align: top">
                @if ($following)
                    <input type="submit" name="unfollow_submit" value="Unfollow" class="btn btn-primary" style="margin-right:0;"/>
                    <input type="hidden" name="unfollow" value="unfollow"/>
                @else
                    <input type="submit" name="follow_submit" value="Follow" class="btn btn-primary" style="margin-right:0"/>
                    <input type="hidden" name="follow"/>
                @endif
            </td>
            <td style="border:0;width: 100%;">
                <div class="follow-notifications" {{ $following ? '' : 'style="display:none"' }}>
                    <p style="margin-bottom: 0px">Notify me when {{ $borrower->getName() }}:<p/>
                    <label for="comment_notify-{{ $borrower->getId() }}" style="float: none;">
                        <input type="checkbox" name="comment_notify" id="comment_notify-{{ $borrower->getId() }}" {{ $notifyComment ? 'checked=checked': '' }}/>
                        posts a new comment
                    </label>
                    <br/>
                    <label for="new_loan_notify-{{ $borrower->getId() }}" style="float: none;">
                        <input type="checkbox" name="new_loan_notify" id="new_loan_notify-{{ $borrower->getId() }}" {{ $notifyLoanApplication ? 'checked=checked': '' }}/>
                        posts a new loan application
                    </label>
                </div>
                <span class="follow-saved" style="display:none;">Your preference has been saved.</span>
            </td>
        </tr>
    </table>
</form>

<style>
    .follow-notifications {
        /*display: none;*/
    }
    .follow-saved {
        color: green;
    }
</style>
