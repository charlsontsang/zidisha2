@extends('layouts.side-menu')

@section('page-title')
Dashboard
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.lender-links')
@stop

@section('page-content')
<h3>Your Project Updates</h3>

<div>
<h3>Comments on My Loans</h3>
@if (count($comments))
    <table class="table">
        <tbody>
        @foreach($comments as $comment)
            <tr>
                <td colspan="2">
                    <a href="{{ route('loan:index', $comment->getBorrower()->getActiveLoanId()) }}">
                        <img src="{{ $comment->getBorrower()->getUser()->getProfilePictureUrl('small-profile-picture') }}" width="100%">
                    </a>
                </td>
                <td>
                    {{ $comment->getMessage() }}
                    <br/><br/>
                    <p class="meta">
                    @if($comment->getUserId() != $comment->getBorrowerId())
                        <a href="{{ route('loan:index', $comment->getBorrower()->getActiveLoanId()) }}">
                         <strong>{{ $comment->getBorrower()->getName() }}</strong>
                        </a>
                        {{ $comment->getUser()->getSubObject()->getName() }}
                        <strong>
                        {{ $comment->getUser()->getSubObject()->getProfile()->getCity() }},
                        {{ $comment->getUser()->getSubObject()->getCountry()->getName() }}
                        </strong>
                        {{ date("d F Y", $comment->getCreatedAt()->getTimeStamp())  }}
                    @else
                        <a href="{{ route('loan:index', $comment->getBorrower()->getActiveLoanId()) }}">
                         <strong>{{ $comment->getUser()->getSubObject()->getName() }}</strong>
                        </a>
                        <strong>
                        {{ $comment->getUser()->getSubObject()->getProfile()->getCity() }},
                        {{ $comment->getUser()->getSubObject()->getCountry()->getName() }}
                        </strong>
                        {{ date("d F Y", $comment->getCreatedAt()->getTimeStamp()) }}
                    @endif
                    </p>
                </td>
            </tr>
        @endforeach
         </tbody>
    </table>
@else
    <p>"Your comment feed is empty."</p>
    <p><strong><a href="{{ route('lend:index') }}">Make a Loan</a></strong></p>
@endif
</div>
@stop
