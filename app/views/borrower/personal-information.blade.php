@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<div class="page-header">
    <h1>Personal Information</h1>
</div>
<hr/>
<div class="borrower-edit-form">
    @foreach($information as $key => $field)
        {{$key}}
    <br/>
    {{$form->isEditable($key)}}
    @endforeach

</div>
@stop
