@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
<div class="row lender-welcome">
    <div class="col-sm-6 col-sm-offset-1 info-page">

        <h1>Welcome to Zidisha!</h1>
        
        <p>
            Ready to make a mega difference with your microloan?  Start exploring available projects, find one (or more!) that you’d like to fund, and make a bid for any portion of the loan.
        </p>
        
    </div>

    <div class="col-sm-5">
          <a class="btn btn-home btn-lg" href="{{ route('lend:index') }}">Browse Projects >></a>
    </div>
</div>

@stop
