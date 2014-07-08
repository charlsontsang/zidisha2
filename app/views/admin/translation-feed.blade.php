@extends('layouts.master')

@section('page-title')
Translation Feed
@stop

@section('content')


<div>
    {{ BootstrapForm::open(array('route' => 'admin:get:translation-feed', 'translationDomain' => 'borrowers', 'method' => 'get')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('language', $form->getLanguages(), Request::query('language')) }}
    {{ BootstrapForm::submit('save') }}

    {{ BootstrapForm::close() }}
</div>
<br><br>

<ul class="nav nav-tabs">
    @foreach(['comments' => 'Comments', 'loans' => 'Loans'] as $key =>
    $Title)
    @if($key == $type)
    <li class="active"><a href="{{ route('admin:get:translation-feed', $key) }}">{{ $Title
            }}</a></li>
    @else
    <li><a href="{{ route('admin:get:translation-feed', $key) }}">{{ $Title }}</a></li>
    @endif
    @endforeach
</ul>
<br><br>
@if($type == 'loans')
@foreach($paginator as $loan)
    <a href="{{ route('admin:get-translate', $loan->getId()) }}">{{
            $loan->getSummary() }}</a>
    <br><br>
@endforeach
@else
@foreach($paginator as $comment)
@include('partials.comments.admin-comment', ['comment' => $comment])
<br>

<br><br>
@endforeach
@endif
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'email' => Request::query('email')])->links() }}
@stop
