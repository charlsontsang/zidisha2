<?php
    /** @var \Zidisha\Loan\Loan $loan */
    $dollar = isset($dollar) ? $dollar : true;
    $stillNeededAmount = $dollar ? $loan->getStillNeededUsdAmount() : $loan->getStillNeededAmount();

    $expired = $interval = $intervalLeft = null;

    $dateInterval = $loan->getIntervalLeft();
    if ($dateInterval->d) {
        $intervalLeft = $dateInterval->d;
        $interval = 'days';
    } elseif ($dateInterval->h) {
        $intervalLeft = $dateInterval->h;
        $interval = 'hours';
    } elseif ($dateInterval->i) {
        $intervalLeft = $dateInterval->i;
        $interval = 'minutes';
    } elseif ($dateInterval->s) {
        $intervalLeft = $dateInterval->s;
        $interval = 'seconds';
    } else {
        $expired = true;
    }
?>

<div class="progress-group">
    <div class="progress">
        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{ $loan->getRaisedPercentage() }}" aria-valuemin="0"
             aria-valuemax="100"
             style="width: {{ $loan->getRaisedPercentage() }}%;">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4 text-center gutter-xs">
            <span class="text-large">{{ $loan->getRaisedPercentage() }}%&nbsp;</span>
            <br/>
            <span class="text-light">
                @lang('borrower.loan.progress.funded')
            </span>
        </div>
        <div class="col-xs-4 text-center gutter-xs">
            <span class="text-large">
                {{ $dollar ? '$' : '' }}{{ $stillNeededAmount->ceil()->format(0) }}&nbsp;
            </span>
            <br/>
            <span class="text-light">
                @lang('borrower.loan.progress.still-needed')
            </span>
        </div>
        <div class="col-xs-4 text-center gutter-xs">
            <span class="text-large">{{ $expired ? '' : $intervalLeft }}</span>
            <br/>
            <span class="text-light">
                @lang($expired ? 'borrower.loan.progress.expired' : "borrower.loan.progress.$interval-left")
            </span>
        </div>
    </div>
</div>
