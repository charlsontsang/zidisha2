@extends('layouts.master')

@section('page-title')
Send test mails
@stop

@section('content')
<div class="row">
    <div class="col-xs-6">
        <h1>Borrower Sms</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>
                    Sms
                </th>
                <th>

                </th>
            </tr>
            </thead>
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
