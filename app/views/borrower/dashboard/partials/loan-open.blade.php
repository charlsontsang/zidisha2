<div class="panel panel-info">

@if($loan->isFullyFunded())

    <div class="panel-heading">
        <h4>@lang('borrower.dashboard.loan-open.fully-funded.title')</h4>
    </div>
    <div class="panel-body">
        <p>
            @lang('borrower.dashboard.loan-open.fully-funded.instructions', ['loanLink' => route('borrower:loan')])
        </p>
    </div>

@else

    <div class="panel-heading">
        <h4>@lang('borrower.dashboard.loan-open.title')</h4>
    </div>
    <div class="panel-body">

        @include('partials/loan-progress', ['loan' => $loan, 'dollar' => false])
        
        <p>@lang('borrower.dashboard.loan-open.tips', ['tipsLink' => route('page:loan-feature-criteria')])</p>
        <p>@lang('borrower.dashboard.loan-open.edit-profile', ['editProfileLink' => route('borrower:edit-profile')])</p>
        
        <div class="btn-group">
            <a class="btn btn-primary" href="{{ route('borrower:loan') }}">
                @lang('borrower.dashboard.loan-page')
            </a>
            <a class="btn btn-default" href="{{ route('loan:index', $loan->getId()) }}">
                @lang('borrower.dashboard.public-loan-page')
            </a>
        </div>

    </div>

@endif

</div>
