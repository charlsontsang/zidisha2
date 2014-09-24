@extends('layouts.master')

@section('page-title')
    Translations
@stop

@section('content')
<h1 class="page-title">
    Translations
</h1>
<ul class="nav nav-tabs" role="tablist">
    @foreach($borrowerLanguages as $code)
        <li class="{{ $code->getLanguageCode() == $languageCode ? 'active' : '' }}"><a href="{{ route('admin:translation:index') }}?languageCode={{$code->getLanguageCode()}}">{{ $code->getName() }}</a></li>
    @endforeach
</ul>
<div class="panel panel-info">
    <div class="panel-body">

        @foreach ($folders as $folder => $files)
            <h2>{{ ucfirst($folder) }}</h2>
            @include('admin.translation.folder-table', compact('files', 'folder'))
        @endforeach
    </div>
</div>
@stop

@section('script-footer')
    <script>
        $('#untranslated').tooltip()
        $('#updated').tooltip()
    </script>
@stop
