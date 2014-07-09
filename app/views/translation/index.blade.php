@extends('layouts.master')

@section('page-title')
    Translations
@stop

@section('content')
<h1>Translations</h1>
<hr/>
<ul class="nav nav-tabs" role="tablist">
    @foreach($languageCodes as $code)
        <li class="{{ $code == $languageCode ? 'active' : '' }}"><a href="{{ route('admin:translation:index') }}?languageCode={{$code}}">{{ $code}}</a></li>
    @endforeach
</ul>

<table class="table table-striped">
    <thead>
        <tr>
            <th>File</th>
            <th>Untranslated</th>
            <th>Updated</th>
        </tr>
    </thead>
    <tbody>
        @foreach($files as $file)
            <tr>
                <td>
                    <a href="{{ route('admin:translation', [$file['filename'], 'languageCode' => $languageCode]) }}">
                        {{ $file['filename'] }}
                    </a>
                </td>
                <td>
                    {{ $file['totalUntranslated'] }}
                </td>
                <td>
                    {{ $file['totalUpdated'] }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop
