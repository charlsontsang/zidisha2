@extends('layouts.master')

@section('content')
<br/>
<style type="text/css">
.image {
    position:relative;
}
.image .text {
    position:absolute;
    text-align: center;
    top:40%;
    left:25%;
    width:500px;
    color:#ffffff;
    font-size: xx-large;
}
</style>
<tr>
<div class="image">
  <img alt="" src="{{ asset('assets/images/404-background.jpg'); }}" />
  <div class="text">
    <p>Oops!</p>
    <p>We couldn't find that page</p>
    <p>ANYWHERE</p>
  </div>
</div>
</tr>
@stop

