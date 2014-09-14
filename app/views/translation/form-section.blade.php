<h{{ $level }} id="{{ $group }}"> {{ ucfirst($title) }} </h{{ $level }}>

@foreach ($labels as $key => $value)
    <?php $groupKey = ltrim($group . '_' . $key, '_') ?>
    @if (is_array($value))
        @include('translation.form-section', ['labels' => $value, 'level' => $level + 1, 'group' => $groupKey, 'title' => $key])
    @else
            {{ BootstrapForm::label($key, null, ['style' => 'display:none;']) }}
            <div class="row">
                <div class="col-md-6">
                    {{ BootstrapForm::textarea(str_replace('.', '_', $groupKey) . '-original', $value, [
                        'label' => false,
                        'rows' => 5,
                        'readonly' => 'readonly',
                    ]) }}
                </div>

                <div class="col-md-6">
                    @if(!$keyToTranslationLabel[$groupKey]->getTranslated())
                        <div class="has-error has-feedback">
                        <span class="glyphicon glyphicon-remove form-control-feedback"></span>
                    @elseif($keyToTranslationLabel[$groupKey]->getUpdated())
                        <div class="has-warning has-feedback">
                        <span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
                    @else
                        <div>
                    @endif
                        {{ BootstrapForm::textarea(str_replace('.', '_', $groupKey), null, ['label' => false, 'rows' => 5]) }}
                    </div>
                </div>
            </div>
    @endif

@endforeach
