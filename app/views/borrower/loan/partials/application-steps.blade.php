<div class="row wizard">
    <?php $i = 1 ?>
    @foreach($steps as $name => $options)
    <div class="col-xs-3 wizard-step {{ $options['class'] }}">
      <div class="text-center wizard-stepnum">Step {{ $i }}</div>
      <div class="progress"><div class="progress-bar"></div></div>
      <a href="#" class="wizard-dot"></a>
      <div class="wizard-info text-center">@lang("loan.application.$name-page.step")</div>
    </div>
    <?php $i += 1 ?>
    @endforeach
</div>
