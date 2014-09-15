@extends('layouts.master')

@section('page-title')
    {{ $group->getName() }}
@stop

@section('content')
<div class="row">
    <div class="col-sm-6 loan-body">
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
                Want to recruit more members for {{{ $group->getName() }}}?  Share the group page:
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
            <a href="{{ route('lender:group', $group->getId()) }}">Go to group page</a>
        </p>
    </div>
</div>

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

        return false;
    });
});
</script>
@stop
