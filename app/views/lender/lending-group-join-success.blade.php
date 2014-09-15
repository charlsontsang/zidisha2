@extends('layouts.master')

@section('page-title')
    {{ $group->getName() }}
@stop

@section('content')
<div class="row">
    <div class="col-sm-6 group-body">
        @if($group->getGroupProfilePicture())
            <div class="pull-left profile-image" href="{{ route('lender:group', $group->getId()) }}"
                style="background-image:url({{ $group->getGroupProfilePicture()->getImageUrl('small-profile-picture') }})" width="100%">
            </div>
        @else
            <div class="pull-left profile-image" href="{{ route('lender:group', $group->getId()) }}"
                style="background-image:url('/assets/images/carousel/mary.jpg')" width="100%">
            </div>
        @endif
    </div>

    <div class="col-sm-6">
        <div class="lead">
            <p>
                <br/><br/>
                You just joined {{{ $group->getName() }}}!
            </p>
            <p>
                Increase members in {{{ $group->getName() }}} by sharing with your friends:
            </p>
            <p>
                <a href="{{$facebookUrl}}" class="btn btn-facebook btn-social share-window">
                    <i class="fa fa-fw fa-facebook"></i>Share
                </a>
                <a href="{{$twitterUrl}}" class="btn btn-twitter btn-social share-window">
                    <i class="fa fa-fw fa-twitter"></i>Tweet
                </a>
                <a href="{{$mailUrl}}" class="btn btn-danger btn-social">
                    <i class="fa fa-fw fa-envelope-o"></i>Email
                </a>
            </p>
        </div>
        <p style="font-size: 18px !important;">
            <br/>
            <a href="{{ route('lender:group', $group->getId()) }}">Go to Group</a>
        </p>
    </div>
</div>

@include('partials._modal', [
    'title' => 'Thanks for sharing!',
    'body' => 'Know someone else who might like to try direct microlending?
               Send them $25 to lend at Zidisha for free!<br/>
               <a href="' . route('lender:invite') . '" class="btn btn-primary">Learn more</a>',
    'id' => 'share-invite-modal'
])
@stop

@section('script-footer')
<script type="text/javascript">
$(function() {
    $('.share-window').click(function(e) {
        e.preventDefault();

        var shareWindow = window.open(
            $(this).attr('href'),
            'fbShareWindow',
            'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0'
        );

        var pollTimer = window.setInterval(function() {
            if (shareWindow.closed !== false) {
                window.clearInterval(pollTimer);
                $('#share-invite-modal').modal();
            }
        }, 200);

        return false;
    });
});
</script>
@stop
