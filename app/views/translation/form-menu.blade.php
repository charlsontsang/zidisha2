<ul>
    @foreach ($labels as $key => $value)
        @if (is_array($value))
        <li class="nav">
            <a href="#{{ $group . '_' . $key }}">{{ ucfirst($key) }}</a>
            @include('translation.form-menu', ['labels' => $value, 'group' => $group . '_' . $key])
        </li>
        @endif
    @endforeach
</ul>
