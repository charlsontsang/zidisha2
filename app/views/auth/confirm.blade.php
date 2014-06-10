@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <h2>Complete signup</h2>

        {{ BootstrapForm::open(['url' => route('facebook:confirm'), 'translationDomain' => 'join.facebook']) }}
        
        {{ BootstrapForm::text('username') }}
        {{ BootstrapForm::textarea('aboutMe') }}
        
        {{ BootstrapForm::submit('submit') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
@stop
