<div class="row wizard">
    <?php $i = 1 ?>
    @foreach(array_slice($steps, 0, 4) as $name => $options)
    <div class="col-xs-3 wizard-step {{ $options['class'] }}">
        <div class="text-center wizard-stepnum">Step {{ $i }}</div>
        <div class="progress"><div class="progress-bar"></div></div>
        <a href="{{ action($options['action']) }}" class="wizard-dot"></a>
        <div class="wizard-info text-center">@lang("borrower.loan-application.progress-bar.$name-page")</div>
    </div>
    <?php $i += 1 ?>
    @endforeach
</div>
