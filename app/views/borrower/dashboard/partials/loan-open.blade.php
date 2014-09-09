@if($loan->isFullyFunded())

    <div class="row">
        <div class="col-xs-12">
            <div class="callout callout-success">
                <h4>@lang('borrower.dashboard.loan-open.fully-funded.title')</h4>
                <p>
                    @lang('borrower.dashboard.loan-open.fully-funded.instructions', ['loanLink' => route('borrower:loan')])
                </p>
            </div>
        </div>
    </div>

@else

    <div class="row">
        <div class="col-md-6">
            @lang('borrower.dashboard.loan-open.title'):
            
            @include('partials/loan-progress', ['loan' => $loan, 'dollar' => false])
        </div>
        <div class="col-md-6">
            <br/>
            <br/>
            <div class="btn-group">
                <a class="btn btn-primary" href="{{ route('borrower:loan') }}">
                    @lang('borrower.dashboard.loan-page')
                </a>
                
                <a class="btn btn-default" href="{{ route('loan:index', $loan->getId()) }}">
                    @lang('borrower.dashboard.public-loan-page')
                </a>
          </div>
        </div>
    </div>
    
    @include('borrower.dashboard.loan-open-tips')

@endif
