@extends('layouts.master')

@section('page-title')
Send Test Emails
@stop

@section('content')
<div class="page-header">
    <h1>Send Test Emails</h1>
</div>
    {{ BootstrapForm::open(array('route' => 'admin:mail:post:mail')) }}

    <br/>
    <div class="row">
        <div class="col-md-4">
            {{ BootstrapForm::text('email', null, ['label' => 'Send emails to']) }}
        </div>
    </div>

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
                                    <button class="btn btn-primary" name="method" type="submit" value="lender#{{ $method }}">Send</button>
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
                        <button class="btn btn-primary" name="method" type="submit" value="borrower#{{ $method }}">Send</button>
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
                    <button class="btn btn-primary" name="method" type="submit" value="admin#{{ $method }}">Send</button>
                </td>
            </tr>
            @endif
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-xs-6">
        <h1>User Mails</h1>
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
            @foreach($userMailerMethods as $method)
            @if($method != '__construct' )
            <tr>
                <td>
                    {{ $method }}
                </td>
                <td>
                    <button class="btn btn-primary" name="method" type="submit" value="user#{{ $method }}">Send</button>
                </td>
            </tr>
            @endif
            @endforeach
            </tbody>
        </table>
    </div>
    {{ BootstrapForm::close() }}
</div>

@stop
