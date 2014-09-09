<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.dashboard.do-more.title')
        </h3>
    </div>
    <div class="panel-body">
        <p>
            @lang('borrower.dashboard.do-more.intro')
        </p>
        <p>
            @lang('borrower.dashboard.do-more.how')
        </p>

        <ol class="omega">
            <li>@lang('borrower.dashboard.do-more.invite', ['inviteLink' => route('borrower:invite')])</li>
            <li>@lang('borrower.dashboard.do-more.comment')</li>
            <li>@lang('borrower.dashboard.do-more.facebook')</li>
            <li>@lang('borrower.dashboard.do-more.volunteer-mentor', ['volunteerMentorLink' => route('page:volunteer-mentor-guidelines')])</li>
            <li>@lang('borrower.dashboard.do-more.forum', ['forumLink' => 'https://www.zidisha.org/forum/'])</li>
        </ol>
    </div>
</div>
