@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Lender Details</h1>
</div>

<img src="{{ $lender->getUser()->getProfilePictureUrl() }}">

<p><strong>Username: </strong> {{ $lender->getUser()->getUsername() }} </p> <br>

<p><strong>About me: </strong> {{ $lender->getProfile()->getAboutMe() }} </p> <br>

<p><strong>Karma: </strong><a href="#" class="karma" data-toggle="tooltip">(?)</a> {{ $karma }} </p> <br>
@stop

@section('script-footer')
<script type="text/javascript">
    $('.karma').tooltip({placement: 'bottom', title: 'Karma is calculated on the basis of the total amount lent by the new members a member has recruited to Zidisha via email invites or gift cards, the number of comments a member has posted in the Zidisha website, and the total amount lent by a member.'})
</script>
@stop