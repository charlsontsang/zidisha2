@extends('layouts.master')

@section('page-title')
Join the global P2P microlending movement
@stop

@section('content')
<h1>Gift Card Terms and Conditions:</h1>

<p>1. A recipient may redeem a gift card by placing a loan bid, then entering the card’s redemption code while completing the
    transaction in the Lending Cart page. When redeemed, the full value of the gift card will be credited to the recipient’s
    lender
    account.</p>

<p>2. If the gift card is not redeemed within twelve months of the card purchase date, the card will automatically convert to an
    unrestricted donation to Zidisha Inc., and can no longer be redeemed by the recipient.</p>

<p>3. It is the responsibility of the purchaser to exercise appropriate caution in safeguarding a gift card and its redemption
    code.
    Gift cards are non-refundable, and replacements cannot be issued for a gift card that is lost or redeemed by someone other
    than the intended recipient.</p>

<p>4. The utilization of gift cards is subject to the general <a href="{{ route('page:terms-of-use') }}">Terms and Conditions</a>
    governing use of the <a
        href="www.zidisha.org">www.zidisha.org</a>
    website
    .</p>

<a href="{{ route('lender:gift-cards:terms-accept') }}" class="btn btn-primary">
    Accept and Continue
</a>

@stop
