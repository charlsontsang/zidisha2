<div>
    <div class="progress xs-collapse">
        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{ $loan->getRaisedPercentage() }}" aria-valuemin="0"
             aria-valuemax="100"
             style="width: {{ $loan->getRaisedPercentage() }}%;">
        </div>
    </div>
    <div class="row sm-collapse">
        <div class="col-xs-12">
            <span>
                <strong>{{ $loan->getRaisedPercentage() }}%</strong> Funded
            </span>
            <img class="leaf" src="{{ '/assets/images/leaf.png' }}"/>
            <span>
                <strong>${{ $loan->getStillNeededUsdAmount()->getAmount() }}</strong> Still Needed
            </span>
        </div>
    </div>
    <div class="row xs-collapse">
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
            <span class="text-large">{{ $loan->getDaysLeft() }}</span>
            <br/>
            <span class="text-light">Days Left</span>
        </div>
    </div>
</div>
