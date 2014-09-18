@extends('layouts.side-menu')

@section('page-title')
Look Up Borrower Account
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
{{ BootstrapForm::open(array('route' => 'admin:borrowers', 'method' => 'get')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('country', $form->getCountries(), Request::query('country'), ['label' => 'Country']) }}
{{ BootstrapForm::select('status', $form->getStatus(), Request::query('status'), ['label' => 'Account Status']) }}
{{ BootstrapForm::text('search', Request::query('search'), ['label' => 'Search']) }}
{{ BootstrapForm::submit('Submit') }}

{{ BootstrapForm::close() }}

<table class="table table-striped" id="borrowers">
    <thead>
    <tr>
        <th>Borrower</th>
        <th>Location</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $borrower)
    <tr>
        <td><a href="{{ route('admin:borrower', $borrower->getUser()->getId()) }}">{{
                $borrower->getFirstName() }} {{ $borrower->getLastName() }}</a>
            <p>{{ $borrower->getUser()->getUsername() }}</p>
            <p>{{ $borrower->getUser()->getEmail() }}</p>
        </td>
        <td>
            {{ $borrower->getProfile()->getCity() }}, {{ $borrower->getCountry()->getName() }}
        </td>
        <td>
            <p>
                <a href="{{ route('admin:borrower', $borrower->getId()) }}">
                    View profile
                </a>
            </p>
            
            @if(Auth::getUser()->isAdmin())
            <p>
                <a href="{{ route('admin:borrower:edit', $borrower->getId()) }}">
                    Edit Profile
                </a>
            </p>
            @endif

            @if($borrower->getUser()->isVolunteerMentor())
                <p>
                    Volunteer Mentor
                </p>
            @else
                @if(Auth::getUser()->isAdmin())
                    <p>
                        <a href="{{ route('admin:add:volunteer-mentor', $borrower->getId()) }}">
                        Make Volunteer Mentor
                        </a>
                    </p>
                @endif
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search'), 'status' => Request::query('status')])->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function() {
            $('#borrowers').dataTable();
    });
</script>
@stop
