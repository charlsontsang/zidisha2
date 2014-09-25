@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div class="highlight highlight-panel">
        	<div class="page-header">
                <h1>@lang('borrower.login.form.forget-password')</h1>
            </div>
            	@lang('borrower.reminders.intro')
            <br/><br/>
            {{ Form::open(array('url' => 'password/remind')) }}
            <div class="form-group">
                {{ Form::label('email', \Lang::get('borrower.reminders.email-password-reset')) }}
                {{ Form::text('email', null, array('class' => 'form-control')) }}
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>

            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
