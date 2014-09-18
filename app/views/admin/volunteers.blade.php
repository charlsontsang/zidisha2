@extends('layouts.side-menu')

@section('page-title')
Active Staff
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
{{ BootstrapForm::open(array('route' => 'admin:volunteers', 'method' => 'get')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('country', $form->getCountries(), Request::query('country'), ['label' => 'Country']) }}
{{ BootstrapForm::text('search', Request::query('search'), ['label' => 'Search']) }}
{{ BootstrapForm::submit('Submit') }}

{{ BootstrapForm::close() }}

<table class="table table-striped">
<thead>
<tr>
    <th>Lender</th>
    <th>Location</th>
    <th>Date Joined</th>
    <th>Volunteer Status</th>
</tr>
</thead>
<tbody>
@foreach($paginator as $lender)
<tr>
    <td><a href="{{ route('lender:public-profile', $lender->getId()) }}">{{
            $lender->getFirstName() }} {{ $lender->getLastName() }}</a>
        <p>{{ $lender->getUser()->getUsername() }}</p>
        <p>{{ $lender->getUser()->getEmail() }}</p>
    </td>
    <td>{{ $lender->getCountry()->getName() }}</td>
    <td>{{ $lender->getUser()->getJoinedAt()->format('M j, Y') }}</td>
    <td>
        @if($lender->getUser()->getSubRole() == \Zidisha\User\User::SUB_ROLE_VOLUNTEER)
            <a href="{{ route('admin:remove:volunteer', $lender->getId()) }}">Remove Volunteer</a>
        @else
            <a href="{{ route('admin:add:volunteer', $lender->getId()) }}">Add Volunteer</a>
        @endif
    </td>
</tr>
@endforeach
</tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search')])->links() }}
@stop
