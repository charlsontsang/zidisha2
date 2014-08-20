@extends('layouts.master')

@section('page-title')
{{ \Lang::get('borrower.borrow.title') }}
@stop

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2 info-page">
        <div class="page-header">
            <h1>{{ \Lang::get('borrower.borrow.title') }}</h1>
        </div>

            <p>{{ \Lang::get('borrower.borrow.information-content-part1') }}</p>

        <h3>{{ \Lang::get('borrower.borrow.heading2') }}</h3>

            <p>{{ \Lang::get('borrower.borrow.paragraph2a') }}</p>
            
            <p>{{ \Lang::get('borrower.borrow.paragraph2b') }}</p>

            <p>{{ \Lang::get('borrower.borrow.advantages') }}</p>

            <ul>
                <li>{{ \Lang::get('borrower.borrow.advantage1') }}</li>
        
                <li>{{ \Lang::get('borrower.borrow.advantage2') }}</li>
            
                <li>{{ \Lang::get('borrower.borrow.advantage3') }}</li>

                <li>{{ \Lang::get('borrower.borrow.advantage4') }}</li>
            </ul>
        
        <h3>{{ \Lang::get('borrower.borrow.requirements-heading') }}</h3>
        
            <p>{{ \Lang::get('borrower.borrow.information-content-part2') }}</p>

            <p>{{ \Lang::get('borrower.borrow.requirements-content-intro') }}</p>
    
            <ul>
                <li>{{ \Lang::get('borrower.borrow.requirements-content-facebook') }}</li>
        
                <li>{{ \Lang::get('borrower.borrow.requirements-content-business') }})</li>
            
                <li>{{ \Lang::get('borrower.borrow.requirements-content-leader') }}</li>
            </ul>

        <h3>{{ \Lang::get('borrower.borrow.how-much-heading') }}</h3>
        
            <p>{{ \Lang::get('borrower.borrow.how-much-content') }}</p>
            
            <p>{{ \Lang::get('borrower.borrow.how-much-max-loan') }}</p>
            
            <p>1. {{ $params['firstLoanVal'] }}
                {{ $params['nxtLoanvalue'] }}</p>
  
        <h3>{{ \Lang::get('borrower.borrow.fees-heading') }}</h3>
        
            <p>{{ \Lang::get('borrower.borrow.fees-content-part1') }}</p>
        
            <p>{{ \Lang::get('borrower.borrow.fees-content-part2') }}</p>
        
            <p>{{ \Lang::get('borrower.borrow.fees-content-part3') }}</p>

        <h3>{{ \Lang::get('borrower.borrow.how-do-heading') }}</h3>
        
            <p>{{ \Lang::get('borrower.borrow.how-do-content') }}</p>

            <p class="text-center">
                <a href="{{ route('borrower:join') }}" class="btn btn-primary btn-lg">
                    {{ \Lang::get('borrower.borrow.how-do-apply') }}
                </a>
            </p>
    </div>
</div>

@stop