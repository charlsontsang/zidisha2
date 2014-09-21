@extends('layouts.side-menu')

@section('page-title')
Contact Us
@stop

@section('menu-title')
About
@stop

@section('menu-links')
@include('partials.nav-links.about-links')
@stop

@section('page-content')
<div class="info-page">
	<p>Questions? Comments? Constructive criticism? We’ll happily take it all — because we love hearing from the Zidisha community. Please feel free to share your ideas and inquiries using any of the means below.</p>
	<p>P.S. Words of encouragement are always welcome, too! (That brightens our volunteers' day like nothing else.)</p>
	<ul>
		<li>For a speedy response to any comment or question, post it on our <a href="https://www.zidisha.org/forum/">Member Forum</a>.</li>
		<li>To reach out to our volunteer staff, email <a href="mailto:service@zidisha.org">service@zidisha.org</a>.</li>
		<li>To send an inquiry or donation by post, mail it to:<br/><br/>
		<address>
			<strong>Zidisha Inc.</strong><br/>
			46835 Muirfield Court #301<br/>
			Sterling, Virginia 20164<br/>
			USA
		</address>
		</li>
	</ul>
</div>
@stop
