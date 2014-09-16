@extends('layouts.master')

@section('page-title')
Exchange rates
@stop

@section('content')
<div class="page-header">
    <h1>Exchange Rates</h1>
</div>

@if(Auth::getUser()->isAdmin())
    <p> Add Exchange Rate for current period </p>

    <br>

    {{ BootstrapForm::open(array('controller' => 'AdminController@postExchangeRates', 'translationDomain' => 'exchange-rate')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('countrySlug', $form->getCountrySlug(), $countrySlug) }}
    {{ BootstrapForm::text('newRate') }}

    {{ BootstrapForm::submit('Save') }}

    {{ BootstrapForm::close() }}
@else
    <p>Exchange Rates for previous periods </p>
@endif
<table class="table table-striped">
    <thead>
    <tr>
        <th>S. No.</th>
        <th>Rate</th>
        <th>From</th>
        <th>To</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $i => $rate)
    <tr>
        <td>{{ $i + 1 + $offset }}</td>
        <td>{{ $rate->getRate() }}</td>
        <td>{{ $rate->getStartDate()->format('d-m-Y') }}</td>
        <td>
            @if($rate->getEndDate())
            {{ $rate->getEndDate()->format('d-m-Y') }}
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
{{ BootstrapHtml::paginator($paginator)->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(function () {
        $("[name=countrySlug]").change(function () {
            window.location = "{{ route('admin:exchange-rates', '') }}/" + $(this).val();
        });
    });
</script>
@stop
