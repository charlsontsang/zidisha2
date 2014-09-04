@extends('layouts.side-menu')

@section('page-title')
Following
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')
<p>  
    Stay up-to-date on the progress of your favorite entrepreneurs.
    The borrowers you’ve funded and opted to follow are listed below.
    Use the buttons and check boxes next to their names to get real-time comment updates
    and receive notifications when they post new loan projects.
</p>
<p>
    P.S. To receive notifications for entrepreneurs you have not yet funded,
    simply click on the “FOLLOW” button in their profiles.
    They’ll automatically get added to this page!
    You can unsubscribe at any time using the "UNFOLLOW" button.
</p>

<br/>

@include('lender.follow.followers', ['followers' => $followingFollowers])

<h2>Entrepreneurs I've Funded</h2>
<br/>

@include('lender.follow.followers', ['followers' => $fundedFollowers])

@stop
