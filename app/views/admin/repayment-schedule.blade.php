@extends('layouts.master')

@section('page-title')
Repayment schedule
@stop

@section('content')
<div class="page-header">
    <h1>
        Repayment Schedule:
        <a href="{{ route('admin:borrower', $borrower->getUser()->getId()) }}">{{ $borrower->getName() }}</a>
    </h1>
</div>

<p>
    Loan: <a href="{{ route('loan:index', $loan->getId()) }}">{{ $loan->getId() }}</a>

    @if($allowPayment)
    {{ BootstrapForm::open(array('route' => 'admin:repayment-schedule', 'class' => 'form-inline', 'id' => 'enter-repayment-form')) }}
    
    {{ BootstrapForm::datepicker('date', null, ['label' => false, 'placeholder' => 'Date paid']) }}
    {{ BootstrapForm::text('amount', null, ['label' => false, 'placeholder' => 'Amount paid']) }}
    
    {{ BootstrapForm::submit('Confirm Payment') }}
    
    {{ BootstrapForm::close() }}
    @endif
</p>

@if($repaymentSchedule)
<table class="table">
    <thead>
    <tr>
        <th colspan="2">Expected Payments</th>
        <th colspan="2">Actual Payments</th>        
    </tr>
    </thead>
    <tbody>
    @foreach($repaymentSchedule as $repaymentScheduleInstallment)
    <tr>
        <td>{{ $repaymentScheduleInstallment->getInstallment()->getDueDate()->format('M j, Y') }}</td>
        <td>{{ $repaymentScheduleInstallment->getInstallment()->getAmount() }}</td>
        <?php $i = 0; ?>
        @if($repaymentScheduleInstallment->getPayments())
        @foreach($repaymentScheduleInstallment->getPayments() as $repaymentScheduleInstallmentPayment)
            @if($i > 0)
            <tr>
                <td></td>
                <td></td>
                <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
                <td>{{ $repaymentScheduleInstallmentPayment->getAmount() }}</td>
            </tr>
            @else
            <td>{{ $repaymentScheduleInstallmentPayment->getPayment()->getPaidDate()->format('M j, Y') }}</td>
            <td>{{ $repaymentScheduleInstallmentPayment->getAmount() }}</td>
            </tr>
            @endif
            <?php $i++; ?>
        @endforeach
        @else
            <td></td>
            <td></td>
        @endif
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td><strong>Total Amount Due</strong></td>
        <td><strong>{{ $repaymentSchedule->getTotalAmountDue() }}</strong></td>
        <td><strong>Total Amount Paid</strong></td>
        <td><strong>{{ $repaymentSchedule->getTotalAmountPaid() }}</strong></td>
    </tr>
    </tfoot>
</table>
@endif

@stop

@if($allowPayment)
@section('script-footer')
<script type="text/javascript">
    $(function () {
        var messageTemplate = 'You are entering a repayment received from this borrower on :date \
in the amount of :amount {{ $loan->getCurrencyCode() }}. \
Please confirm this is correct, and that this repayment has not already been entered in the schedule above.';
        
        $('#enter-repayment-form').on('submit', function() {
            var $this = $(this),
                $date = $this.find('[name=date]'),
                date = $date.val(),
                $amount = $this.find('[name=amount]'),
                amount = $amount.val(),
                message = messageTemplate;

            $date.parent().removeClass('has-error');
            $amount.parent().removeClass('has-error');
            if (!date) {
                $date.parent().addClass('has-error');
                return false;
            }
            if (!amount) {
                $amount.parent().addClass('has-error');
                return false;
            }
            
            message = message.replace(':date', date).replace(':amount', amount)
            
            return confirm(message);
        });
    });
</script>
@stop
@endif