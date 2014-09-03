@extends('layouts.master')

@section('content')
    <div class="page-header">
        @if($loan)
        <h1>
            {{ $loan->getSummary() }}
            <small>{{ $loan->getAppliedAt()->format('M j, Y') }}</small>
        </h1>
        
        <a href="{{ action('LoanController@getIndex', [ 'loanId' => $loan->getId() ]) }}" class="btn btn-primary">
            View loan profile page
        </a>
        @else
        <h1>
            No loans yet.
        </h1>

        <a class="btn btn-primary" href="{{ route('borrower:loan-application') }}">
            Apply for a loan
        </a>
        @endif

        @if($loans)
        <span class="dropdown pull-right">
            <a class="btn btn-default" data-toggle="dropdown" href="#">All loans</a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                @foreach($loans as $_loan)
                <li role="presentation" class="{{ $loan == $_loan ? 'active'  : ''}}">
                    <a role="menuitem" tabindex="-1" href="{{ route('borrower:loan', $_loan->getId()) }}">
                        {{ $_loan->getAppliedAt()->format('M j, Y') }}
                    </a>
                </li>
                @endforeach
            </ul>
        </span>
        @endif
    </div>
@stop
