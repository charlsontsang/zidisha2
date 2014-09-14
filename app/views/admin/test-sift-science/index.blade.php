@extends('layouts.master')

@section('page-title')
Send Sift Science Events
@stop

@section('content')
<div class="page-header">
    <h1>Send Sift Science Events</h1>
</div>
<div class="row">
    <div class="col-xs-6">
        <table class="table table-striped">
            <tbody>
            @foreach($siftScienceEvents as $method)
            @if($method != '__construct' )
            <tr>
                <td>
                    {{ $method }}
                </td>
                <td>
                    {{ BootstrapForm::open(array('route' => 'admin:post:test:sift-science')) }}
                    {{ BootstrapForm::hidden('method', $method) }}
                    {{ BootstrapForm::submit('Send') }}
                    {{ BootstrapForm::close() }}
                </td>
            </tr>
            @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
