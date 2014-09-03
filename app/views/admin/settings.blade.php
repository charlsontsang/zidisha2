@extends('layouts.side-menu')

@section('page-title')
Other Settings
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
{{ BootstrapForm::open(['route' => 'admin:settings']) }}
{{ BootstrapForm::populate($settingsForm) }}

<ul class="nav nav-tabs" role="tablist">
    <?php $i = 0; ?>
    @foreach($groups as $group => $_)
    <li class="{{ $i ? '' : 'active' }}"><a href="#{{ snake_case($group) }}" role="tab" data-toggle="tab">{{ $group }}</a></li>
    <?php $i += 1; ?>
    @endforeach
</ul>

<br/>

<div class="tab-content">
<?php $i = 0; ?>
@foreach($groups as $group => $groupSettings)
    <div class="tab-pane {{ $i ? '' : 'active' }}" id="{{ snake_case($group) }}">
        @foreach($groupSettings as $name => $options)
            {{ BootstrapForm::input($options['type'], str_replace('.', '_', $name), null, [
                'label' => $options['label'], 
                'description' => $options['description'],
                'prepend' => $options['prepend'],
                'append' => $options['append']
            ]) }}
        @endforeach
    </div>
    <?php $i += 1; ?>
@endforeach
</div>

{{ BootstrapForm::submit('Submit') }}

{{ BootstrapForm::close() }}
@stop

@section('script-footer')
<script type="text/javascript">
$(function() {
    $('.nav-tabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
});
</script>
@stop
