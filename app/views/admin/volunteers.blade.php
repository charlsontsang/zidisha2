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

<p>
    We have {{ $count }} active staff!&nbsp;&nbsp;&nbsp;
    <a href="#" id="toggle-filter">Filter results</a>
</p>

<div id="filter" class="collapse">
    {{ BootstrapForm::open(array('route' => 'admin:volunteers', 'method' => 'get')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country'), ['label' => 'Country']) }}
    {{ BootstrapForm::text('search', Request::query('search'), ['label' => 'Search']) }}
    {{ BootstrapForm::submit('Submit') }}

    {{ BootstrapForm::close() }}
</div>

<table class="table table-striped" id="staff">
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
    <td>
        <p><a href="{{ route('lender:public-profile', $lender->getUser()->getUserName()) }}">
            @if (!empty($lender->getFirstName()))
                {{ $lender->getFirstName() }} {{ $lender->getLastName() }}
            @else
                {{ $lender->getUser()->getUsername() }}
            @endif
        </a></p>
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

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#staff').dataTable();
    });
    $('#toggle-filter').click(function () {
        $('#filter').collapse('toggle');
        return false;
    });
</script>
@stop
