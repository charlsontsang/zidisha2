@extends('layouts.master')

@section('page-title')
Invite Your Friends To Zidisha
@stop

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2 info-page">
        <div style="text-align: center; margin: 50px;">
            <h2>
                We're sorry, the new lender invite program is currently inactive.
            </h2>
            <p>
                It will be resumed when new invite credits become available.
            </p>
                <a href="{{ route('lender:how-it-works') }}">How the invite program works</a>
            </p>
        </div>
    </div>
</div>
@stop