<ul>
    @foreach ($labels as $name => $value)
        @if (is_array($value))
        <li class="nav">
            <a href="#{{ $group ? $group . '_' . $name : $name }}">{{ ucfirst($name) }}</a>
            @include('translation.form-menu', ['labels' => $value, 'group' => $group ? $group . '_' . $name : $name])
        </li>
        @endif
    @endforeach
</ul>
