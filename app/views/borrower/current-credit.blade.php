@extends('layouts.side-menu')

@section('page-title')
    @lang('borrower.credit-limit.title')
@stop

@section('menu-title')
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
<p>
    @lang('borrower.credit-limit.intro')
</p>
<p>
    @lang('borrower.credit-limit.note')
</p>

<p class="well">
    @lang('borrower.credit-limit.current-credit-limit'): {{ $calculator->getCreditLimit() }}
</p>

<p>
    @lang('borrower.credit-limit.how-determined'):
</p>

<ol>
    <li>
        <p>
            @lang('borrower.credit-limit.base-credit-limit'): {{ $calculator->getBaseCreditLimit() }}            
        </p>
        
        @if($calculator->isFirstLoan())
        <p>
            <em>@lang('borrower.credit-limit.first-loan', $replacements)</em>
        </p>
        @elseif($calculator->hasRepaidLate())
        <p>
            <em>@lang('borrower.credit-limit.repaid-late', $replacements)</em>
        </p>
        @elseif($calculator->hasInsufficientRepaymentRate())
        <p>
            <em>@lang('borrower.credit-limit.insufficient-repayment-rate', $replacements)</em>
        </p>
        @elseif($calculator->hasInsufficientLoanLength())
        <p>
            <em>@lang('borrower.credit-limit.insufficient-loan-length', $replacements)</em>
        </p>
        @elseif($calculator->hasRepaidTooEarly())
        <p>
            <em>@lang('borrower.credit-limit.repaid-too-early', $replacements)</em>
        </p>
        @else
        <p>
            <em>@lang('borrower.credit-limit.sufficient-repayment-rate', $replacements)</em>
        </p>
        @endif
    </li>
    <li>
        <p>
            <a href="{{ route('borrower:invites') }}" target="_blank">
                @lang('borrower.credit-limit.invite-credit')
            </a>: {{ $calculator->getInviteCredit() }}
        </p>
    </li>
    <li>
        <p>
            @lang('borrower.credit-limit.vm-credit'): {{ $calculator->getVMCredit() }}            
        </p>
    </li>
    @if($calculator->getCommentCredit()->isPositive())
    <li>
        <p>
            @lang('borrower.credit-limit.comment-credit'): {{ \Zidisha\Currency\Money::create(0, $calculator->getCurrency()) }}            
        </p>
        <p>
            <em>
                @lang('borrower.credit-limit.comment-credit-note', ['commentCredit' =>  $calculator->getCreditLimit()])
            </em>
        </p>
    </li>
    @endif
    @if($calculator->getTotalBonusCredit()->greaterThan($calculator->getMaximumBonusCredit()))
    <li>
        <p>
            @lang('borrower.credit-limit.max-bonus-credit'): {{ $calculator->getMaximumBonusCredit() }}            
        </p>
    </li>
    @endif
</ol>

<p>
    <strong>
        @lang('borrower.credit-limit.total-credit-limit'): {{ $calculator->getCreditLimit() }}        
    </strong>
</p>

<p>
    @lang('borrower.credit-limit.how-increase'):
</p>

<ul>
    <li>
        @lang('borrower.credit-limit.maintain-repayment-rate', $replacements)
    </li>
    <li>
        @lang('borrower.credit-limit.make-final-repayment')
    </li>
    <li>
        @choice('borrower.credit-limit.current-loan-length', $calculator->getMinLoanLength(), $replacements)
    </li>
    <li>
        @lang('borrower.credit-limit.distribute-repayments')
    </li>
</ul>

@stop
