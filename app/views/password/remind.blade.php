@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
    	<div class="page-header">
            <h1>@lang('borrower.login.form.forget-password')</h1>
        </div>
        	@lang('borrower.reminders.intro')
        <br/><br/>
        {{ Form::open(array('url' => 'password/remind')) }}
        <div class="form-group">
            {{ Form::label('username', \Lang::get('borrower.reminders.username-or-password')) }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
        </div>
        <button type="submit" class="btn btn-default">Submit</button>

        {{ Form::close() }}
    </div>
</div>
@stop
