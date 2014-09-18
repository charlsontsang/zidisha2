@extends('layouts.master')

@section('page-title')
@lang('borrower.text.assigned-members.title')
@stop

@section('content')
<div class="page-header">
<h1>@lang('borrower.text.assigned-members.title')</h1>
</div>
<br/><br/>

<h3>@lang('borrower.text.assigned-members.pending')</h3>
<p>@lang('borrower.text.assigned-members.pending-instructions')</p>
<br/>
<table class="table table-striped">
	<tbody>
		@foreach ($data['pendingMembers'] as $pendingMember)
				<tr>
					<td>
						{{ $pendingMember->getName() }}
					</td>
					<td>
					    <a href="{{ route('admin:borrower:personal-information', $pendingMember->getUser()->getUsername()) }}">
					    @lang('borrower.text.assigned-members.review-profile')
					    </a>
					</td>
				</tr>
            @endforeach
	</tbody>
</table>
<br/><br/><br/>

<h3>@lang('borrower.text.assigned-members.arrears')</h3>
<p>@lang('borrower.text.assigned-members.arrears-instructions')</p>
<br/>
<table class="table table-striped">
	<tbody>
		@foreach ($data['assignedMembers'] as $assignedMember)
		        @if ($data['repaymentService']->getRepaymentSchedule($assignedMember->getActiveLoan())->getOverDueInstallmentCount() > 1)
				<tr>
					<td>
						{{ $assignedMember->getName() }}
					</td>
					<td>
						<a href="{{ route('admin:vm:repayment-schedule', $assignedMember->getId()) }}">
                        @lang('borrower.text.assigned-members.view-repayment-schedule')
                        </a>
					</td>
					<td>
					    <a href="{{ route('admin:borrower:personal-information', $assignedMember->getUser()->getUsername()) }}">
					    @lang('borrower.text.assigned-members.view-contact-information')
					    </a>
					</td>
					<td>
					    @if($data['borrowerService']->hasVMComment($borrower, $assignedMember))
                            <p>@lang('borrower.text.assigned-members.comment-posted')</p>
                            <a href="{{ route('loan:index', $assignedMember->getActiveLoanId()) }}">
                            @lang('borrower.text.assigned-members.post-another-comment')
                            </a>
                        @else
                            <p>@lang('borrower.text.assigned-members.no-comment')</p>
                            <a href="{{ route('loan:index', $assignedMember->getActiveLoanId()) }}">
                            @lang('borrower.text.assigned-members.post-comment')
                            </a>
                        @endif
					</td>
				</tr>
				@endif
            @endforeach
	</tbody>
</table>
<br/><br/><br/>

<h3>@lang('borrower.text.assigned-members.current')</h3>
<p>@lang('borrower.text.assigned-members.current-instructions')</p>
<br/>
<table class="table table-striped">
	<tbody>
		@foreach ($data['assignedMembers'] as $assignedMember)
		        @if ($data['repaymentService']->getRepaymentSchedule($assignedMember->getActiveLoan())->getOverDueInstallmentCount() <= 1)
				<tr>
					<td>
						{{ $assignedMember->getName() }}
					</td>
					<td>
						<a href="{{ route('admin:vm:repayment-schedule', $assignedMember->getId()) }}">
                        @lang('borrower.text.assigned-members.view-repayment-schedule')
                        </a>
					</td>
					<td>
					    <a href="{{ route('admin:borrower:personal-information', $assignedMember->getUser()->getUsername()) }}">
					    @lang('borrower.text.assigned-members.view-contact-information')
					    </a>
					</td>
					<td>
                        <a href="{{ route('loan:index', $assignedMember->getActiveLoanId()) }}">
                        @lang('borrower.text.assigned-members.view-loan')
                        </a>
					</td>
				</tr>
				@endif
            @endforeach
	</tbody>
</table>
<br/><br/><br/>
@stop

