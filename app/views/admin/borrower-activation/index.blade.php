@extends('layouts.master')

@section('page-title')
Activate Borrowers
@stop

@section('content')
<div class="page-header">
    <h1>
        Activate Borrowers
    </h1>
</div>

<table class="table table-striped" id="pending-activation">
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
            Dialing code: {{ BootstrapHtml::number($borrower->getProfile()->getPhoneNumber(), $borrower->getCountry()->getCountryCode()) }}
        </td>
        <td>
            @if($borrower->getCommunityLeader())
            {{ $borrower->getCommunityLeader()->getName() }}
            <br/>
            {{ BootstrapHtml::number($borrower->getCommunityLeader()->getPhoneNumber(), $borrower->getCountry()->getCountryCode()) }}
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
            <br/>
            <a href="{{ route('admin:borrower-activation:edit', $borrower->getId()) }}">
                <i class="fa fa-pencil-square-o fa-lg"></i>
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function() {
            $('#pending-activation').dataTable({
                searching: true,
                'order': [[ 5, 'asc' ]]
            });
    });
</script>
@stop
