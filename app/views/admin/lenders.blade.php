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
<p>
    Total lenders : {{ $totalLenders }}
    <br/>
    Active lenders : {{ $activeLenders }}
    <br/>
    Logged in during past 2 months : {{ $activeLendersInPastTwoMonths }}
    <br/>
    Number of lenders using automated lending : {{ $lenderUsingAutomatedLending }}
    <br/>
    Total lender credit available : {{ $totalLenderCredit }}
</p>

<div class="page-header">
    <h1>Lenders</h1>
</div>
>>>>>>> add headers in search pages of admin
<div>
    {{ BootstrapForm::open(array('route' => 'admin:lenders', 'translationDomain' => 'lenders', 'method' => 'get')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
    {{ BootstrapForm::text('search', Request::query('search')) }}
    {{ BootstrapForm::submit('Search') }}

    {{ BootstrapForm::close() }}
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Lender</th>
        <th>Location</th>
        <th>Date Joined</th>
        <th>Last Login</th>
        <th>Last check in email</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $lender)
    <tr>
        <td><a href="{{ route('lender:public-profile', $lender->getUser()->getUserName()) }}">{{
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
                Lender has not logged in yet
            @endif
        </td>
        
        <td>
         {{ BootstrapForm::open(array('route' => ['admin:last-check-in-email:lender', $lender->getId() ])) }}
         {{ BootstrapForm::datepicker('lastCheckInEmail') }}
         {{ BootstrapForm::submit('Submit') }}
         {{ BootstrapForm::close() }}
         
         <br/>
         <hr/>
         Last check in email :
         @if($lender->getLastCheckInEmail())
            {{ $lender->getLastCheckInEmail()->format('m/d/Y') }}         
         @else
          Not available 
         @endif
        </td>
        <td>
            <p>
                @if($lender->getUser()->getActive()) 
                    Active
                @else
                    Inactive         
                @endif             
            </p>
            
            @if($lender->getUser()->getActive())
               {{ BootstrapForm::open(array('route' => ['admin:deactivate:lender', $lender->getId() ])) }}
               {{ BootstrapForm::submit('Deactivate') }}
               {{ BootstrapForm::close() }}
            @else
               {{ BootstrapForm::open(array('route' => ['admin:activate:lender', $lender->getId() ])) }}
               {{ BootstrapForm::submit('Activate') }}
               {{ BootstrapForm::close() }}
            @endif             
            <br/>
           {{ BootstrapForm::open(array('route' => ['admin:delete:lender', $lender->getId() ])) }}
           {{ BootstrapForm::submit('Delete') }}
           {{ BootstrapForm::close() }}
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search')])->links() }}
@stop
