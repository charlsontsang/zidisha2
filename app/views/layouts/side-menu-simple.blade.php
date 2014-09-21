@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-3 col-md-4">
        <ul class="nav side-menu" role="complementary">
          <h4>@yield('menu-title')</h4>
          @yield('menu-links')
        </ul>
    </div>

    <div class="col-sm-9 col-md-8">

    	<h1 class="page-title">@yield('page-title')</h1>

        @yield('page-content')

	  </div>
</div>
@stop
