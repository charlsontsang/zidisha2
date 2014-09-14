<ul>
    @foreach ($labels as $key => $value)
        @if (is_array($value))
        <li class="nav">
            <a href="#{{ $group ? $group . '_' . $key : $key }}">{{ ucfirst($key) }}</a>
            @include('translation.form-menu', ['labels' => $value, 'group' => $group ? $group . '_' . $key : $key])
        </li>
        @endif
    @endforeach
</ul>
