@extends('layouts.master')

@section('page-title')
Send Test SMS
@stop

@section('content')
<div class="page-header">
    <h1>Send Test SMS</h1>
</div>
<div class="row">
    <div class="col-xs-6">
        <table class="table table-striped">
            <tbody>
            @foreach($borrowerSms as $method)
            @if($method != '__construct' )
            <tr>
                <td>
                    {{ $method }}
                </td>
                <td>
                    {{ BootstrapForm::open(array('route' => 'admin:sms:post:sms')) }}
                    {{ BootstrapForm::hidden('sms', 'borrower') }}
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
