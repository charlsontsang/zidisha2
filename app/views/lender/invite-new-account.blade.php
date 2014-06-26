@if($inviter->hasProfilePicture())
    <img style="width:100px" src="{{ $inviter->getProfilePictureUrl() }}"/>
    <br/>
@endif

<h3 style="font-size: 16px;padding:12px 0">
    You have received a $25 credit from
    <a href="{{ route('lender:public-profile', $inviter->getUsername()) }} ">
        {{ $inviter->getUsername()}}
    </a>
    to fund a loan of your choice.
</h3>

To use this credit, open the profile page of an entrepreneur you'd like to support and click Lend.
<br/>
<br/>

<a class="btn" href="{{ route('lend:index') }}">View Entrepreneurs</a>
