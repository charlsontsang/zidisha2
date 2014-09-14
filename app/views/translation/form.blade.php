@extends('layouts.master')

@section('content')
<div class="row">
    <a href=" {{ route('admin:translation:index') }} ">Translation Index</a>
    <br/>
    <a href="#" id="toggle-label">Toggle Labels</a>
</div>

<div class="row">
    <div class="col-md-10">
        {{ BootstrapForm::open(['route' => ['admin:translation:post', $filename, $languageCode], 'id' => 'labels-form']) }}
        {{ BootstrapForm::model($defaultValues) }}

        @include('translation.form-section',['labels' => $fileLabels, 'level' => 2, 'group' => '', 'title' => $filename])

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
