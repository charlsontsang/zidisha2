<h{{ $level }} id="{{ $group }}"> {{ ucfirst($title) }} </h{{ $level }}>

@foreach ($labels as $name => $value)
    <?php $groupName = ltrim($group . '_' . $name, '_') ?>
    @if (is_array($value))
        @include('translation.form-section', ['labels' => $value, 'level' => $level + 1, 'group' => $groupName, 'title' => $name])
    @else
            {{ BootstrapForm::label($name, null, ['style' => 'display:none;']) }}
            <div class="row">
                <div class="col-md-6">
                    {{ BootstrapForm::textarea(str_replace('.', '_', $groupName) . '-original', $value, [
                        'label' => false,
                        'rows' => 5,
                        'readonly' => 'readonly',
                    ]) }}
                </div>

                <div class="col-md-6">
                    @if(!$nameToTranslationLabel[$groupName]->getTranslated())
                        <div class="has-error has-feedback">
                        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                    @elseif($nameToTranslationLabel[$groupName]->getUpdated())
                        <div class="has-warning has-feedback">
                        <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                    @else
                        <div>
                    @endif
                        {{ BootstrapForm::textarea(str_replace('.', '_', $groupName), null, ['label' => false, 'rows' => 5]) }}
                    </div>
                </div>
            </div>
    @endif

@endforeach
