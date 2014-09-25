<html><body>

<h2>Request</h2>
<table>
    @foreach($request as $k => $v)
        <tr>
            <td style="vertical-align: top"><b>{{ $k }}</b></td>
            <td><?php is_scalar($v) ? print($v) : var_dump($v) ?></td>
        </tr>
    @endforeach
</table>

<h2>Session</h2>
<table>
    @foreach($session as $k => $v)
    <tr>
        <td style="vertical-align: top"><b>{{ $k }}</b></td>
        <td><?php is_scalar($v) ? print($v) : var_dump($v) ?></td>
    </tr>
    @endforeach
</table>

<h2>Input</h2>
<table>
    @foreach($input as $k => $v)
        <tr>
            <td style="vertical-align: top"><b>{{ $k }}</b></td>
            <td><?php is_scalar($v) ? print($v) : var_dump($v) ?></td>
        </tr>
    @endforeach
</table>

<h2>Cookies</h2>
<table>
    @foreach($cookies as $k => $v)
    <tr>
        <td style="vertical-align: top"><b>{{ $k }}</b></td>
        <td><?php is_scalar($v) ? print($v) : var_dump($v) ?></td>
    </tr>
    @endforeach
</table>

<h2>User</h2>
<table>
    @foreach($user as $k => $v)
    <tr>
        <td style="vertical-align: top"><b>{{ $k }}</b></td>
        <td><?php is_scalar($v) ? print($v) : var_dump($v) ?></td>
    </tr>
    @endforeach
</table>

<hr/>

@foreach($exceptions as $exception)
    <b>
        @if(is_object($exception))
         {{ get_class($exception) }}
        @endif
    </b>
    <p style="color:red;">
        {{ $exception->getMessage() }}
    </p>

    <p>
        <i>{{ $exception->getFile() }}:{{ $exception->getLine() }}</i>
    </p>

    <hr/>
@endforeach

{{ $trace }}

</body></html>
