@extends('layouts.master')

@section('page-title')
Following
@stop

@section('content')

<div class="page-header">
    <h1>Entrepreneurs I'm Following</h1>
</div>

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
