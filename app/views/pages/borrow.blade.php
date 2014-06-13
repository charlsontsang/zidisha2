@extends('layouts.master')

@section('page-title')
    @lang('borrow.page-title')
@stop

@section('content')

<div class="row">
<h2>INFORMATION FOR BORROWERS</h2>
<a href=""></a>
</div>
<a href="{{ route('borrower:join') }}" class="btn btn-primary">
            Apply
        </a>
@stop
