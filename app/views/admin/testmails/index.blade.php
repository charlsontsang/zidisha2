@extends('layouts.master')

@section('page-title')
    Send test mails
@stop

@section('content')
    <div class="row">
        <div class="col-xs-6">
            <h1>Lender Mails</h1>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>
                                Mail
                            </th>
                            <th>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lenderMailerMethods as $method)
                        @if($method != '__construct' )
                            <tr>
                                <td>
                                    {{ $method }}
                                </td>
                                <td>
                                    {{ BootstrapForm::open(array('route' => 'admin:mail:post:mail')) }}
                                    {{ BootstrapForm::hidden('mailer', 'lender') }}
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
        <div class="col-xs-6">
            <h1>Borrower Mails</h1>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>
                        Mail
                    </th>
                    <th>

                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($borrowerMailerMethods as $method)
                @if($method != '__construct' )
                <tr>
                    <td>
                        {{ $method }}
                    </td>
                    <td>
                        {{ BootstrapForm::open(array('route' => 'admin:mail:post:mail')) }}
                        {{ BootstrapForm::hidden('mailer', 'borrower') }}
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

<!-- New Row -->
<div class="row">
    <div class="col-xs-6">
        <h1>Admin Mails</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>
                    Mail
                </th>
                <th>

                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($adminMailerMethods as $method)
            @if($method != '__construct' )
            <tr>
                <td>
                    {{ $method }}
                </td>
                <td>
                    {{ BootstrapForm::open(array('route' => 'admin:mail:post:mail')) }}
                    {{ BootstrapForm::hidden('mailer', 'admin') }}
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
