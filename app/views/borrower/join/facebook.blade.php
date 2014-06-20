@extends('layouts.master')

@section('content')
        <a href="{{$facebookJoinUrl}}"> Verify with Facebook </a>
        @if(Session::get('BorrowerJoin.countryCode') == 'BF')
        {{ link_to_action('BorrowerJoinController@getSkipFacebook', 'Skip') }}
        @endif
@stop

