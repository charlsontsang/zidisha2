<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.dashboard.volunteer-mentor.title')
        </h3>
    </div>
    <div class="panel-body">
        <p>
            @lang('borrower.dashboard.volunteer-mentor.intro', ['volunteerMentorLink' => route('page:volunteer-mentor-guidelines')])
        </p>
        <p class="omega">
            <strong>@lang('borrower.dashboard.volunteer-mentor.name')</strong>
            <a href="{{ route('borrower:public-profile', $borrower->getId()) }}">{{ $volunteerMentor->getName() }}</a>
            
            <br/>
            
            <strong>@lang('borrower.dashboard.volunteer-mentor.telephone')</strong>
            {{ $volunteerMentor->getProfile()->getPhoneNumber() }}
        </p>
    </div>
</div>
