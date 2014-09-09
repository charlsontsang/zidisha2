<div class="row">
    @foreach($lenders as $lender)
    <div class="col-xs-4">
        <div class="lender-thumbnail">
            <a href="{{ $lender->getUser()->getProfileUrl() }}">
                @if($lender->getUser()->getProfilePictureUrl())
                <img src="{{ $lender->getUser()->getProfilePictureUrl() }}" alt="">
                @else
                <img src="{{ asset('/assets/images/profile-default1.jpg') }}" alt="">
                @endif
            </a>
            <h3>
                <a href="{{ route('lender:public-profile', $lender->getUser()->getUserName()) }}">
                    {{ $lender->getUser()->getUserName() }}</a>
            </h3>
            <p>
                @if($lender->getProfile()->getCity())
                {{ $lender->getProfile()->getCity() }},&nbsp;
                @endif
                {{ $lender->getCountry()->getName() }}
            </p>
        </div>
    </div>
    @endforeach
</div>
