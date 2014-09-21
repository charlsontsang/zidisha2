@extends('layouts.side-menu')

@section('page-title')
@lang('borrower.menu.your-invites')
@stop

@section('menu-title')
@lang('borrower.menu.links-title')
@stop

@section('menu-links')
@include('partials.nav-links.borrower-links')
@stop

@section('page-content')
<p>
    @lang('borrower.invite.invites-message', ['minRepaymentRate' => $minRepaymentRate, 'borrowerInviteCredit' => $borrowerInviteCredit])
</p>
<p>
    @lang('borrower.invite.success-rate'):
    {{ BootstrapHtml::tooltip('borrower.invite.success-rate-tooltip') }}
    &nbsp;&nbsp;
    <strong>{{ $successRate }}%</strong>
</p>
<p>
    @lang('borrower.invite.bonus-earned'):
    {{ BootstrapHtml::tooltip('borrower.invite.bonus-earned-tooltip') }}
    &nbsp;&nbsp;
    <strong>{{ $bonusEarned }}</strong>
</p>

    <table class="table table-striped no-more-tables">
        <thead>
        <tr>
            <th>
                @lang('borrower.invite.name')
            </th>
            <th>
                @lang('borrower.invite.email')
            </th>
            <th>
                @lang('borrower.invite.status')
            </th>
            <th>
                @lang('borrower.invite.repayment-rate')
            </th>
            <th>
                @lang('borrower.invite.bonus-credit')
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($invites as $invite)
        <tr>
            <td data-title="Name">{{ $invite['name'] }}</td>
            <td data-title="Email">{{ $invite['email'] }}</td>
            <td data-title="Status">
                <p>
                    {{ $invite['status'] }}
                </p>
        <!-- allow removal only if invitee has not yet raised a loan -->
                @if (empty($invite['repaymentRate'])) 
                    <p><a href="{{ route('borrower:delete-invite', $invite['id']) }}">
                        @lang('borrower.invite.delete')
                    </a></p>
                @endif
            </td>
            <td data-title="RepaymentRate">{{ $invite['repaymentRate'] }}</td>
            <td data-title="BonusCredit">{{ $invite['bonusCredit'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
@stop
