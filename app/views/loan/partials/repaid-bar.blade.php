<div class="row">
    <div class="col-xs-9">
        <div class="progress" style="margin: 0 !important;">
            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{ floor($loan->getPaidPercentage()) }}" aria-valuemin="0"
                 aria-valuemax="100"
                 style="width: {{ floor($loan->getPaidPercentage()) }}%;">
            </div>
        </div>
    </div>
    <div class="col-xs-3">
        <strong>{{ floor($loan->getPaidPercentage()) }}%</strong> Repaid
    </div>
</div>
