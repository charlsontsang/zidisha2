@extends('layouts.master')


@section('page-title')
@lang('borrower.text.assigned-members.title')
@stop

@section('content')
<div class="page-header">
<h1>@lang('borrower.text.assigned-members.title')</h1>
</div>
<br/><br/>

<h3>@lang('borrower.text.assigned-members.pending')</h3>
<p>@lang('borrower.text.assigned-members.pending-instructions')</p>
<br/>
<table class="table-striped">
	<tbody>

	</tbody>
</table>
<br/><br/><br/>

<h3>@lang('borrower.text.assigned-members.arrears')</h3>
<p>@lang('borrower.text.assigned-members.arrears-instructions')</p>
<br/>
<table class="table-striped">
	<tbody>

	</tbody>
</table>
<br/><br/><br/>

<h3>@lang('borrower.text.assigned-members.current')</h3>
<p>@lang('borrower.text.assigned-members.current-instructions')</p>
<br/>
<table class="table-striped">
	<tbody>

	</tbody>
</table>
<br/><br/><br/>
@stop

