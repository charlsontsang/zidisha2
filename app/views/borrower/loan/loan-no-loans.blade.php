@extends('layouts.master')

@section('page-content')
<div class="panel panel-info">
    <div class="panel-heading">
        <h4>
            @lang('borrower.loan.page.no-loan')
        </h4>
    </div>
    <div class="panel-body">
        {{ \Lang::get('borrower.loan.no-loan.no-loan-message') }} 
        <a class="btn btn-primary pull-right" href="{{ route('borrower:loan-application') }}">
		    @lang('borrower.loan.page.apply')
		</a>
    </div>
</div>
@stop
