<div class="panel panel-info">
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
            @if($volunteerMentor->getLastLoanId())
            <a href="{{ route('loan:index', $volunteerMentor->getLastLoanId()) }}">
             {{ $volunteerMentor->getName() }}</a>
             @else
                 {{ $volunteerMentor->getName() }}
            @endif
            <br/>
            
            <strong>@lang('borrower.dashboard.volunteer-mentor.telephone')</strong>
            {{ BootstrapHtml::number($volunteerMentor->getProfile()->getPhoneNumber(), $volunteerMentor->getCountry()->getCountryCode()) }}
        </p>
    </div>
</div>
