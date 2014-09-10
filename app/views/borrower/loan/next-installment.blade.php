@if($repaymentSchedule->getNextDueInstallment())
<?php $nextInstallment = $repaymentSchedule->getNextDueInstallment(); ?>
<div class="callout callout-info">
    <h4>
        @lang('borrower.loan.loan-active.next-installment.title')
    </h4>
    <p>
        @lang('borrower.loan.loan-active.next-installment.instructions', [
            'date'  => $nextInstallment->getDueDate()->format('M j, Y'),
            'amount' => $nextInstallment->getAmount()
        ])
    </p>
</div>
@endif 
