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
    {{ BootstrapForm::open([
        'route' => ['admin:enter-repayment', $loan->getId()],
        'class' => 'form-inline',
        'id' => 'enter-repayment-form',
    ]) }}
    {{ BootstrapForm::populate($form) }}
    
    {{ BootstrapForm::datepicker('date', null, ['label' => false, 'placeholder' => 'Date paid']) }}
    {{ BootstrapForm::text('amount', null, ['label' => false, 'placeholder' => 'Amount paid']) }}
    
    {{ BootstrapForm::submit('Confirm Payment') }}
    
    {{ BootstrapForm::close() }}
    @endif
</p>

@if($repaymentSchedule)
    @include('loan/partials/repayment-schedule', compact('repaymentSchedule'))
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