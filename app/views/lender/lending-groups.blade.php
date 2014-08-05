@extends('layouts.master')

@section('page-title')
Lending Groups
@stop

@section('content')
<div class="row">
    <div class="col-md-12 info-page">
        <div class="page-header">
            <h1>Lending Groups</h1>
        </div>
        <p>
            Lending Groups maximize their impact by combining forces to do more good. Whether the members of a Lending Group share a common passion, support similar causes, or simply come from the same country, they all join together to make microlending miracles happen. Check them out below — and get involved!
            <br/><br/>
        </p>
        @if(Auth::check() && Auth::getUser()->isLender())
        <a href="{{ route('lender:groups:create') }}" class="btn btn-primary">
           Start a new group
        </a>
    </div>
</div>
@endif
@if($paginator != null)
<table class="table table-striped no-more-tables">
    <thead>
    <tr>
        <th>Group Name</th>
        <th>Impact This Month</th>
        <th>Impact Last Month</th>
        <th>Impact All Time</th>
        <th width="30%">About This Group</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $group)
    <tr>
        <td data-title="Group Name">{{ $group->getName() }}
            @if($group->getGroupProfilePicture())
                <img src="{{ $group->getGroupProfilePicture()->getImageUrl('small-profile-picture') }}" alt=""/>
            @endif
        </td>
        <td data-title="Impact This Month">{{ $groupsImpacts[$group->getId()]['totalImpactThisMonth'] }}</td>
        <td data-title="Impact Last Month">{{ $groupsImpacts[$group->getId()]['totalImpactLastMonth'] }}</td>
        <td data-title="Impact All Time">{{ $groupsImpacts[$group->getId()]['totalImpact'] }}</td>
        <td>{{ $group->getAbout() }}</td>
        <td><a href="{{ route('lender:group', $group->getId()) }}">View Profile</a></td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop
