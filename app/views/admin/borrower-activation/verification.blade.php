<div class="row" id="verification">
    <div class="col-xs-12">
        <h2>Step 2: Verification</h2>
    </div>
</div>

<div class="row">
    <div class="col-xs-4">
        {{ BootstrapForm::open(['route' => ['admin:borrower-activation:verification', $borrower->getId()]]) }}
        {{ BootstrapForm::populate($verificationForm) }}

        {{ BootstrapForm::radio('isEligibleByAdmin', true, null, ['label' => 'Yes']) }}
        {{ BootstrapForm::radio('isEligibleByAdmin', false, null, ['label' => 'No']) }}

        {{ BootstrapForm::submit('Submit') }}

        {{ BootstrapForm::close() }}
    </div>
</div>
