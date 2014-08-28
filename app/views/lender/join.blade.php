@extends('layouts.master')

@section('content')
{{ link_to_route('borrower:join', 'Create a borrower account') }}
<br/>
<br/>
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        @include('lender.join-form')
    </div>
</div>

@include('partials._modal', [
    'title' => 'Terms of use',
    'template' => 'lender.terms-of-use',
    'id' => 'termsOfUseModal',
    'scrollable' => true
])
@stop