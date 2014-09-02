@extends('layouts.master')

@section('page-title')
Trust and Security
@stop

@section('content')
<div class="row">
        <div class="col-sm-3 col-md-4">
            <ul class="nav side-menu" role="complementary">
              <h4>About</h4>
              @include('partials.nav-links.about-links')
            </ul>
        </div>

        <div class="col-sm-9 col-md-8 info-page">
        <div class="page-header">
            <h1>Trust and Security</h1>
        </div>

        <p>First, an important warning: Zidisha is not a financial institution or an investment service. This means that this is not a safe place to store your savings or financial assets. So we suggest that you treat lending through Zidisha as a philanthropic activity — and as always, don’t lend out any funds that you cannot afford to lose.</p>

        <p>We make lending easy, but we don’t take it lightly — so we take a variety of measures to strengthen the security of our lending platform and put confidence behind your compassion.</p>

        <p>First, we verify the identity of our borrowers by:</p>
        <ul>
            <li>Collecting their precise residential addresses, telephone numbers, and national identity numbers before allowing them to post a loan application.</li>
            <li>Requiring them to verify their online identity by linking an active personal Facebook account to their Zidisha account. The public Facebook pages are displayed to prospective lenders in the loan profile pages.</li>
            <li>Disbursing loans through banks and payment services that check government-issued identity cards to verify recipients' identity.</li>
            <li>Requiring borrowers to provide telephone numbers of several local contacts that can vouch for their identity and reputation.</li>
            <li>Working with a leading online fraud detection service to screen borrower accounts for suspicious activity before activating new borrowers and disbursing loans.</li>
            <li>Requesting local volunteers to provide a second screening of borrower applications when available.</li>
        </ul>
        <p>Then we help incentivize timely and responsible repayments by:</p>
        <ul>
            <li>Limiting loan sizes to small amounts for new borrowers and increasing those limits only if they maintain high on-time repayment rates over time.</li>
            <li>Linking borrowers' credit limits to the repayment performance of other borrowers they referred to Zidisha.</li>
            <li>Displaying borrowers' historical on-time repayment rates — and those of the borrowers who invited them to join Zidisha, if applicable — so prospective lenders can evaluate their repayment track records.</li>
            <li>Displaying comments and feedback ratings left by previous lenders on borrowers' loan profile pages.</li>
            <li>Notifying and requesting mediation from local contacts, volunteers, and leaders in the borrower's community in the event of default.</li>
        </ul>

        <p>That said, it's important to understand that Zidisha is a purely online service without local offices or loan officers. We are a low-cost nonprofit community (managed by volunteers). Our organization is responsible only for maintaining the Zidisha.org website as a platform for the transactions that take place between its members, and it is up to the lenders, not Zidisha, to determine if a business is viable and worthy of being funded. We do not undertake many of the activities that are performed by traditional banks and microfinance institutions, including:</p>

        <ul>
            <li>Fact-checking or guarantees that the information provided by borrowers is accurate.</li>
            <li>Physical visits to borrowers.  Local volunteers may optionally visit borrowers, but this is not guaranteed or required.</li>
            <li>Verification and reporting to lenders of how loan funds have been spent.</li>
            <li>Repayment enforcement, guarantees or providing reports of collection efforts to lenders. Personalized collection efforts may be undertaken and reported to lenders on a case-by-case basis depending on the availability of volunteers.</li>
        </ul>
    </div>
</div>
@stop
