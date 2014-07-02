@extends('layouts.master')

@section('page-title')
Pending Activation
@stop

@section('content')

<table class="table table-striped">
    <thead>
    <tr>
        <th>Name</th>
        <th>Location</th>
        <th>Contacts</th>
        <th>Community Leader</th>
        <th>Completed On</th>
        <th>Last Modified</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $borrower)
    <tr>
        <td>
            {{ $borrower->getName() }}
        </td>
        <td>
            {{ $borrower->getCountry()->getName() }}
            <br/>
            {{ $borrower->getProfile()->getCity() }}
        </td>
        <td>
            {{ $borrower->getUser()->getEmail() }}
            <br/>
            Dialing code: {{ $borrower->getProfile()->getPhoneNumber() }}
        </td>
        <td>
            @if($borrower->getCommunityLeader())
            {{ $borrower->getCommunityLeader()->getName() }}
            <br/>
            {{ $borrower->getCommunityLeader()->getPhoneNumber() }}
            @endif
        </td>
        <td>
            {{ $borrower->getCreatedAt()->format('M d, Y') }}
        </td>
        <td>
            {{ $borrower->getUpdatedAt()->format('M d, Y') }}
        </td>
        <td>
            TODO
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop

