@foreach($followers as $follower)
<div class="row">
    <div class="col-xs-2">
        <img src="{{ $follower->getBorrower()->getUser()->getProfilePictureUrl() }}" width="100%" alt=""/>
    </div>
    <div class="col-xs-10">
        <div class="row">
            <div class="col-sm-6">
                @if($follower->getBorrower()->getLastLoanId())
                    <a href="{{ route('loan:index', $follower->getBorrower()->getLastLoanId()) }}">
                     {{ $follower->getBorrower()->getName() }}</a>
                 @else
                     {{ $follower->getBorrower()->getName() }}
                @endif
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