<div>
    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="{{ $loan->getRaisedPercentage() }}" aria-valuemin="0"
             aria-valuemax="100"
             style="width: {{ $loan->getRaisedPercentage() }}%;">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4 text-center gutter-xs">
            <span class="text-large">{{ $loan->getRaisedPercentage() }}%</span>
            <br/>
            <span class="text-light">Funded</span>
        </div>
        <div class="col-xs-4 text-center gutter-xs">
            <span class="text-large">${{ $loan->getStillNeededUsdAmount()->getAmount() }}</span>
            <br/>
            <span class="text-light">Still Needed</span>
        </div>
        <div class="col-xs-4 text-center gutter-xs">
            <span class="text-large">{{ $loan->getDaysLeft() }}%</span>
            <br/>
            <span class="text-light">Days Left</span>
        </div>
    </div>
</div>