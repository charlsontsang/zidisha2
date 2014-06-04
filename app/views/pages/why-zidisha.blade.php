@extends('layouts.master')

@section('page-title')
Why Zidisha?
@stop

@section('content')
<div class="page-header">
    <h1>Why Zidisha?</h1>
</div>

<p>Entrepreneurs in developing nations face a difficult problem: With average microfinance interest rates upwards of 40% or more, the high cost of financial services keeps them from growing their earnings enough to improve their families' standards of living.</p>

<p>You can change that by becoming a lender. When you support microfinance through social investments in a Zidisha entrepreneur, you not only fund a microfinance project, but you also fight poverty in that entrepreneur's family and community.
</p>

<h4>Direct Peer-to-Peer Microlending</h4>

<p>Unlike other internet microfinance websites that simply allow web users to fund microfinance programs, we offer the ability to interact directly with the entrepreneurs. Lenders can post questions and comments, while borrowers share the progress of their micro investments and let lenders know about the impact that their microfinance investing provides.</p>

<div class="text-center">
    <img src="{{ asset('assets/images/pages/why-zidisha/Zidisha_Arrows.png'); }}" alt="" />
</div>

<h4>Zidisha Offers Dramatically Lower Microfinance Interest Rates to the Entrepreneurs</h4>

<p>While other microfinance services charge borrowers interest rates upwards of 40% or more, our direct peer-to-peer microlending model reduces the cost of Zidisha loans to just a fraction of this. The lower microfinance interest rates translate into larger profits for the entrepreneurs - profits that go directly towards the well-being of their families and their communities.</p>

<p>Please view our <a href="{{ route('page:how-it-works') }}">How It Works</a> page to learn more.</p>
<p>Ready to make a loan?  Go to our <a href="TODO"> Lend</a> page to browse available loan applications and lend to an entrepreneur.</p>
@stop
