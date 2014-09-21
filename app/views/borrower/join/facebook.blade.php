@extends('layouts.master')

@section('content')
<div class="page-header">
    <h1>
        @lang('borrower.join.form.title')
    </h1>
</div>

<p>
    @lang('borrower.join.form.facebook-intro', ['buttonText' => \Lang::get('borrower.join.form.facebook-button')])
</p>

<em>
    @lang('borrower.join.form.facebook-note')
</em>

<br/><br/>

<p>
    <a href="{{ $facebookJoinUrl }}" class="btn btn-facebook">
        <span class="fa fa-facebook fa-lg fa-fw"></span>
        @lang('borrower.join.form.facebook-button')
    </a>
</p>

<p>
    @if(Session::get('BorrowerJoin.countryCode') == 'BF')
    <a href="{{ action('BorrowerJoinController@getSkipFacebook') }}">
        @lang('borrower.join.form.facebook-skip')
    </a>
    @endif
</p>
@stop

