@foreach($followers as $follower)
<div class="row">
    <div class="col-xs-2">
        <img src="{{ $follower->getBorrower()->getUser()->getProfilePictureUrl() }}" width="100%" alt=""/>
    </div>
    <div class="col-xs-10">
        <div class="row">
            <div class="col-sm-6">
                <a href="{{ $follower->getBorrower()->getActiveLoan() ? 
                route('loan:index', $follower->getBorrower()->getActiveLoanId()) :
                route('borrower:public-profile', $follower->getBorrower()->getId()) }}">
                    {{ $follower->getBorrower()->getName() }}
                </a>
                <br/>
                {{ $follower->getBorrower()->getCountry()->getName() }}
                <br/>
                <br/>

                @if($follower->getBorrower()->getActiveLoan())
                <p>
                    {{ $follower->getBorrower()->getActiveLoan()->getSummary() }}
                </p>
                @endif
            </div>
            <div class="col-sm-6">
                @include('lender.follow.follower', [
                    'lender' => $lender,
                    'borrower' => $follower->getBorrower(),
                    'follower' => $follower,
                    'enableFollow' => false
                ])
            </div>
        </div>
    </div>
</div>
@endforeach