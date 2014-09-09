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

<div class="callout callout-warning">
    <ul class="list-unstyled omega">
        <li>@lang('borrower.dashboard.loan-open.tips', ['tipsLink' => route('page:loan-feature-criteria')])</li>
        <li>@lang('borrower.dashboard.loan-open.edit-profile', ['editProfileLink' => route('borrower:edit-profile')])</li>
    </ul>
</div>
