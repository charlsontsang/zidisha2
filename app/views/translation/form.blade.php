@extends('layouts.master')

@section('content')
<div class="row">
    <a href=" {{ route('admin:translation:index') }} ">Translation Index</a>
</div>

<div class="row">
    <div class="col-md-10">
        {{ BootstrapForm::open() }}
        {{ BootstrapForm::model($defaultValues) }}

        @include('translation.form-section',['labels' => $fileLabels, 'level' => 2, 'group' => $filename, 'title' => $filename])

        {{ BootstrapForm::submit('Save') }}
        {{ BootstrapForm::close() }}
    </div>

    <div class="col-md-2">
        <nav>
            @include('translation.form-menu', ['labels' => $fileLabels, 'group' => $filename])
        </nav>
    </div>
</div>
@stop
