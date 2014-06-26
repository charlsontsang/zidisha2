@extends('layouts.master')

@section('page-title')
Invite Your Friends To Zidisha
@stop

@section('content')
<div style="text-align: center;">
    <h2>
        We're sorry, the new member invite program is currently inactive.
    </h2>
</div>

<div class="post">
    <div class="entry" style="text-align: center;margin-top: 20px;">
        <div>
            It will be resumed when new invite credits become available.
            &nbsp;&nbsp;
            <a href="{{ route('lender:how-it-works') }}">Learn more</a>
        </div>
    </div>
</div>
@stop