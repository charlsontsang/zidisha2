@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
	<div class="page-header">
	    <h1><div style="text-align: center;">
	    Welcome
	    </div></h1>
	</div>

<div class="row">
   <p>
    Thanks for joining us, and welcome!
   </p> 
    
    <p>
    Go to our  <a href="{{ route('lend:index') }}">Fundraising Loans</a> page to browse open loan applications and choose an entrepreneur to fund. 
    </p>
    
    <p>
    Please don't hesitate to <a href="{{ route('page:contact') }}">let us know</a> if you would like help getting started. We look forward to hearing from you. 
    </p>
    
    <p>
    Best wishes,
    </p>
    
    <p>
    Zidisha Team
    </p>
    
</div>

@stop
