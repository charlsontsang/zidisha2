@if($loan->isFullyFunded())
<div class="panel panel-info">
    <div class="panel-heading">
        <h4>@lang('borrower.dashboard.loan-open.fully-funded.title')</h4>
    </div>
    <div class="panel-body">
        <p>
            @lang('borrower.dashboard.loan-open.fully-funded.instructions', ['loanLink' => route('borrower:loan')])
        </p>
    </div>
</div>

@else

@include('borrower.dashboard.partials.loan-open-tips')

@endif
