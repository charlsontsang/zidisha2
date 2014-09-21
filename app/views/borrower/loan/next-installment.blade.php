@if($repaymentSchedule->getNextDueInstallment())

<?php $nextInstallment = $repaymentSchedule->getNextDueInstallment(); ?>

<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
        	@lang('borrower.loan.loan-active.next-installment.title')
        </h3>
    </div>
    <div class="panel-body">
	    <p>
	        @lang('borrower.loan.loan-active.next-installment.instructions', [
	            'date'  => $nextInstallment->getDueDate()->format('M j, Y'),
	            'amount' => $nextInstallment->getAmount()
	        ])
	    </p>
    </div>
</div>

@endif 
