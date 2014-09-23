@extends('layouts.master')

@section('page-title')
Invite Your Friends to Zidisha
@stop

@section('content')
<div>
    <h2>Invite Your Friends to Zidisha</h2>
</div>
{{ BootstrapForm::open(array('route' => 'lender:post-invite-google', 'translationDomain' => 'invite',
'id' => 'invite-google')) }}

<table class="table table-striped">
    <thead>
    <tr>
        <th>Invite</th>
        <th>Name</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($contacts as $contact)
    <tr>
        <td>{{ BootstrapForm::checkbox('emails[]', $contact['email']) }}</td>
        <td>{{ $contact['name'] }}</td>
        <td>{{ $contact['email'] }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
<button id="invite-google" class="btn btn-primary" type="submit">Invite Friends</button>
<a href="{{ route('lender:welcome') }}" >
    Skip this step
</a>
{{ BootstrapForm::close() }}

@stop
