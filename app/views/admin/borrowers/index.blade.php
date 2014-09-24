@extends('layouts.side-menu-simple')

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
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            {{ BootstrapForm::open(array('route' => 'admin:borrowers', 'method' => 'get')) }}
            {{ BootstrapForm::populate($form) }}

            {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country'), ['label' => 'Country']) }}
            {{ BootstrapForm::select('status', $form->getStatus(), Request::query('status'), ['label' => 'Account status']) }}
            {{ BootstrapForm::text('search', Request::query('search'), ['label' => 'Search for city, name, phone or email']) }}
            {{ BootstrapForm::submit('Submit') }}

            {{ BootstrapForm::close() }}
        </h3>
    </div>
    <div class="panel-body">
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
                <td>
                    <p>
                        <a href="{{ route('admin:borrower', $borrower->getUser()->getId()) }}">{{
                        $borrower->getFirstName() }} {{ $borrower->getLastName() }}</a>
                    </p>
                    <p>Tel. {{ $borrower->getProfile()->getPhoneNumber() }}</p>
                    <p>{{ $borrower->getUser()->getEmail() }}</p>
                </td>
                <td>
                    <p>
                        {{ $borrower->getProfile()->getCity() }}
                    </p>
                    <p>
                        {{ $borrower->getCountry()->getName() }}
                    </p>
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
    </div>
</div>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search'), 'status' => Request::query('status')])->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function() {
            $('#borrowers').dataTable();
    });
</script>
@stop
