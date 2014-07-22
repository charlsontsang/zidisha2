@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
       @include('auth.login-form')
    </div>
</div>
@stop
