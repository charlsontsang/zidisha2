@extends('layouts.side-menu')

@section('page-title')
Donate
@stop

@section('menu-title')
About
@stop

@section('menu-links')
@include('partials.nav-links.about-links')
@stop

@section('page-content')
<div class="info-page">
	<p>Zidisha isn’t just an amazing way to make a difference with microloans — we’re also a nonprofit organization. That means we rely on voluntary contributions and super-generous donations from our lenders and supporters to help cover operating expenses, like web hosting, bank fees, telephone costs, regulatory fees, and the ongoing development of our web platform.</p>
	<p>We don’t charge any service fee to lenders. Instead, we ask that those who participate contribute what they can to support our growth. (And we’re incredibly thankful when they do!)</p>
	<p>Zidisha is a 501(c)(3) nonprofit organization, and your donation to us is tax deductible in the United States.</p>
	<p>Below are three easy ways you can donate.</p>
	
	<h3>Option One: Donate Via Check</h3>
	<p>If you’re the holder of a United States bank account, you can donate by check to save on payment transfer fees. You may make a check out to Zidisha Inc. and mail it to our address below. Make sure to indicate on the check that the payment is a donation.</p>
	
	<address>
		<strong>Zidisha Inc.</strong><br/>
		46835 Muirfield Court #301<br/>
		Sterling, Virginia 20164<br/>
		USA
	</address>

	<h3>Option Two: Donate Via Credit Card or PayPal</h3>
	<p>Zidisha members in all countries may make donations via PayPal or credit card in the <a href="{{ route('lender:funds') }}">Transfer Funds</a> page when logged in.</p>

	<h3>Option Three: Donate Via Bank Transfer</h3>
	<p>Please instruct your bank to transfer the desired amount to Zidisha Incorporated. Here are our recipient bank details:</p>
	<p>Name of Bank: Wells Fargo Bank<br/>Wire Routing Transit Number: 121000248<br/>SWIFT Code: WFBIUS6S<br/>City, State: San Francisco, California, United States<br/>Account Number: 2952542906<br/>Title of Account: Zidisha Inc.</p>
	<p>Then just send us an email to <a href="mailto:service@zidisha.org">service@zidisha.org</a> to let us know that the transfer is intended as a donation.</p>

	<p>Thanks so much for your support!</p>
</div>
@stop
