@extends('layouts.master')

@section('content')
    <div class="page-header">
        <h1>
            {{ $loan->getSummary() }}
            <small>{{ $loan->getAppliedAt()->format('M j, Y') }}</small>
        </h1>
        
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
