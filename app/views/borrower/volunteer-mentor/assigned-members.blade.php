@extends('layouts.side-menu-simple')

@section('page-title')
	@lang('borrower.text.assigned-members.title')
@stop

@section('menu-title')
	@lang('borrower.menu.links-title')
@stop

@section('menu-links')
	@include('partials.nav-links.borrower-links')
@stop

@section('page-content')

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.text.assigned-members.pending')
        </h3>
    </div>
    <div class="panel-body">
		<p>@lang('borrower.text.assigned-members.pending-instructions')</p>
		<br/>
		<table class="table table-striped" id="pending-members">
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
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.text.assigned-members.arrears')
        </h3>
    </div>
    <div class="panel-body">
		<p>@lang('borrower.text.assigned-members.arrears-instructions')</p>
		<br/>
		<table class="table table-striped" id="arrears-members">
			<tbody>
				@foreach ($data['assignedMembers'] as $assignedMember)
				        @if ($data['repaymentService']->getRepaymentSchedule($assignedMember->getLastLoan())->getOverDueInstallmentCount() > 1)
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
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            @lang('borrower.text.assigned-members.current')
        </h3>
    </div>
    <div class="panel-body">
		<p>@lang('borrower.text.assigned-members.current-instructions')</p>
		<br/>
		<table class="table table-striped" id="current-members">
			<tbody>
				@foreach ($data['assignedMembers'] as $assignedMember)
			        @if ($data['repaymentService']->getRepaymentSchedule($assignedMember->getLastLoan())->getOverDueInstallmentCount() <= 1)
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
    </div>
</div>

@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#pending-members').dataTable({
            'searching': true
        });
        $('#arrears-members').dataTable({
            'searching': true
        });
        $('#current-members').dataTable({
            'searching': true
        });
    });
</script>
@stop

