@extends('layouts.master')

@section('page-title')
Translation Feed
@stop

@section('content')

<div class="page-header">
    <h1>
        Translation Feed
    </h1>
</div>

<div>
    {{ BootstrapForm::open(array('route' => 'admin:get:translation-feed', 'method' => 'get', 'class' => 'form-inline')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('language', $form->getLanguages(), Request::query('language'), ['label' => false]) }}
    {{ BootstrapForm::submit('Go') }}

    {{ BootstrapForm::close() }}
</div>
<br><br>

<div class="row">
    <div class="col-sm-8 loan-body">
        <ul class="nav nav-tabs nav-justified">
            @foreach(['comments' => 'Comments', 'loans' => 'Loans'] as $key => $title)
                @if($key == $type)
                    <li class="active"><a href="{{ route('admin:get:translation-feed', $key) }}">{{ $title }}</a></li>
                @else
                    <li><a href="{{ route('admin:get:translation-feed', $key) }}">{{ $title }}</a></li>
                @endif
            @endforeach
        </ul>

        <div class="tab-content">
            <div class="comments">
                <ul class="media-list">
                    @if($type == 'loans')
                        @foreach($paginator as $loan)
                            <li class="comment media">
                                <div class="pull-left">
                                    <a href="{{ $loan->getBorrower()->getUser()->getProfileUrl() }}">
                                        <img class="media-object" src="{{ $loan->getBorrower()->getUser()->getProfilePictureUrl() }}" alt="">
                                    </a>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <a href="{{ $loan->getBorrower()->getUser()->getProfileUrl() }}">
                                            {{ $loan->getBorrower()->getName() }}
                                        </a>
                                        <small>{{ $loan->getAppliedAt()->format('M d, Y') }}</small>
                                    </h4>
                                    <p>
                                        {{ $loan->getSummary() }}
                                    </p>
                                    <a href="{{ route('admin:get-translate', $loan->getId()) }}">Translate this loan</a>
                                </div>
                            </li>
                            <hr/>
                        @endforeach
                    @else
                        @foreach($paginator as $comment)
                            @include('partials.comments.admin-comment', ['comment' => $comment])
                            <hr/>
                        @endforeach
                    @endif
                </ul>
            </div>
            {{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'email' => Request::query('email')])->links() }}
        </div>
    </div>
</div>
@stop
