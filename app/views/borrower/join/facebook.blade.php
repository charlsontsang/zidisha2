@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <h1 class="page-title">
            @lang('borrower.join.form.title')
        </h1>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @lang('borrower.join.form.online-identity')
                </h3>
            </div>
            <div class="panel-body">

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

                    @if(Session::get('BorrowerJoin.countryCode') == 'BF')
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>
                        <a href="{{ action('BorrowerJoinController@getSkipFacebook') }}">
                            @lang('borrower.join.form.facebook-skip')
                        </a>
                    </strong>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@stop

