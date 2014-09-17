@extends('layouts.master')

@section('content')
<div class="page-header">
    <h1>
        {{ ucfirst($folder) }}: {{ ucfirst($filename) }} <small>{{ $languageCode }}</small>
    </h1>
</div>


<div class="clearfix">
    <a href=" {{ route('admin:translation:index') }} " class="btn btn-primary">Back to Translations</a>
    <a href="#" id="toggle-label" class="btn btn-default pull-right">Toggle Labels</a>
</div>

<div class="row">
    <div class="col-md-10">
        {{ BootstrapForm::open(['route' => ['admin:translation:post', $folder, $filename, $languageCode], 'id' => 'labels-form']) }}
        {{ BootstrapForm::model($defaultValues) }}

        @include('translation.form-section',['labels' => $fileLabels, 'level' => 2, 'group' => '', 'title' => $filename])

        {{ BootstrapForm::submit('Save') }}
        {{ BootstrapForm::close() }}
    </div>

    <div class="col-md-2">
        <br/>
        <br/>
        <nav>
            @include('translation.form-menu', ['labels' => $fileLabels, 'group' => ''])
        </nav>
    </div>
</div>
@stop

@section('script-footer')
<script>
    $(function() {
        $("#toggle-label").click(function() {
            $( "label" ).toggle();
        });
        function autoheight(a) {
            if (!$(a).prop('scrollTop')) {
                do {
                    var b = $(a).prop('scrollHeight');
                    var h = $(a).height();
                    $(a).height(h - 5);
                }
                while (b && (b != $(a).prop('scrollHeight')));
            };
            $(a).height($(a).prop('scrollHeight') + 20);
        }
        $('#labels-form textarea').each(function() {
            autoheight(this);
        }); 
    });
</script>
@stop
