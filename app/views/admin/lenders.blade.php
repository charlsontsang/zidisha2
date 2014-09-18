@extends('layouts.side-menu')

@section('page-title')
Look Up Lender Account
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
<div>
    {{ BootstrapForm::open(array('route' => 'admin:lenders')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country'), ['label' => 'Country']) }}
    {{ BootstrapForm::text('search', Request::query('search'), ['label' => 'Search']) }}
    {{ BootstrapForm::submit('Submit') }}

    {{ BootstrapForm::close() }}
</div>

<table class="table table-striped" id="lenders">
    <thead>
    <tr>
        <th>Lender</th>
        <th>Location</th>
        <th>Date Joined</th>
        <th>Last Login</th>
        <th>Last Check-In Email</th>
        <th>Status</th>
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
        <td>
            {{ $lender->getCountry()->getName() }}
        </td>
        <td>
            {{ $lender->getUser()->getCreatedAt()->format('M d, Y') }}
        </td>
        <td>
            @if($lender->getUser()->getLastLoginAt())
                {{ $lender->getUser()->getLastLoginAt()->format('M d, Y') }}
            @else
                {{ $lender->getUser()->getCreatedAt()->format('M d, Y') }}
            @endif
        </td>
        
        <td>
         @if($lender->getLastCheckInEmail())
            {{ $lender->getLastCheckInEmail()->format('m/d/Y') }}         
         @else
            None
         @endif

         <br/><br/>

         {{ BootstrapForm::open(array('route' => ['admin:last-check-in-email:lender', $lender->getId() ])) }}
         {{ BootstrapForm::datepicker('lastCheckInEmail', null, ['label' => '', 'placeholder' => 'Last check-in']) }}
         {{ BootstrapForm::submit('Save') }}
         {{ BootstrapForm::close() }}

        </td>
        <td>
            <p>
                @if($lender->getUser()->getActive()) 
                    Active
                @else
                    Inactive         
                @endif             
            </p>

            @if(Auth::getUser()->isAdmin())
            <p>
                @if($lender->getUser()->getSubRole() == \Zidisha\User\User::SUB_ROLE_VOLUNTEER)
                    <a href="{{ route('admin:remove:volunteer', $lender->getId()) }}">Remove Volunteer</a>
                @else
                    <a href="{{ route('admin:add:volunteer', $lender->getId()) }}">Make Volunteer</a>
                @endif
            </p>
            <p>
                @if($lender->getUser()->getActive())
                <a href="{{ route('admin:deactivate:lender', $lender->getId()) }}">
                    Deactivate lender account
                </a>
                @else
                <a href="{{ route('admin:activate:lender', $lender->getId()) }}">
                    Activate lender account
                </a>
                @endif
            </p>
            <p> 
                <a href="{{ route('admin:delete:lender', $lender->getId()) }}">
                    Delete account
                </a>
            </p>
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
    $(document).ready(function() {
            $('#lenders').dataTable();
    });
</script>
@stop
