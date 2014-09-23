@extends('layouts.side-menu-simple')

@section('page-title')
    {{ $loan->getSummary() }}
@stop

@section('menu-title')
    @lang('borrower.menu.links-title')
@stop

@section('menu-links')
    @include('partials.nav-links.borrower-links')
@stop


@section('page-content')
    <div class="page-header">
        
        <a href="{{ action('LoanController@getIndex', [ 'loanId' => $loan->getId() ]) }}" class="btn btn-primary">
            @lang('borrower.loan.page.public-loan-page')
        </a>
        
        <span class="dropdown pull-right">
            
            @if(count($loans) > 1)
            <a class="btn btn-default" data-toggle="dropdown" href="#">
                @lang('borrower.loan.page.your-loans')
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                @foreach($loans as $_loan)
                <li role="presentation" class="{{ $loan == $_loan ? 'active'  : ''}}">
                    <a role="menuitem" tabindex="-1" href="{{ route('borrower:loan', $_loan->getId()) }}">
                        {{ $_loan->getAppliedAt()->format('M j, Y') }}
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </span>
    </div>
@stop
