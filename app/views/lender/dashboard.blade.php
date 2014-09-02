@extends('layouts.master')

@section('page-title')
Dashboard
@stop

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>Quick Links</h4>
          	@include('partials.nav-links.lender-links')

	        @if (count($lendingGroups)>0)
	          @foreach($lendingGroups as $lendingGroup)
	            <li><a href="{{ route('lender:group', $lendingGroup->getId()) }}">{{ $lendingGroup->getName() }}</a></li>
		       @endforeach
		    @else
		        <li><a href="{{ route('lender:groups') }}">Lending Groups</a></li>
	        @endif
        </ul>
    </div>

    <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>Your Project Updates</h1>
        </div>
    </div>
</div>
@stop
