@extends('layouts.master')

@section('page-title')
Exchange Rates
@stop

@section('content')
<div class="page-header">
    <h1>Exchange Rates</h1>
</div>

@if(Auth::getUser()->isAdmin())
    {{ BootstrapForm::open(array('controller' => 'AdminController@postExchangeRates')) }}
    {{ BootstrapForm::populate($form) }}

    {{ BootstrapForm::select('countrySlug', $form->getCountrySlug(), $countrySlug, ['label' => 'Currency']) }}
    {{ BootstrapForm::text('newRate', '', ['label' => 'New Exchange Rate']) }}

    {{ BootstrapForm::submit('Save') }}

    {{ BootstrapForm::close() }}
@endif

<table class="table table-striped">
    <thead>
    <tr>
        <th>From</th>
        <th>To</th>
        <th>Rate</th>
    </tr>
    </thead>
    <tbody>
    @foreach($paginator as $i => $rate)
    <tr>
        <td>{{ $rate->getStartDate()->format('M j, Y') }}</td>
        <td>
            @if($rate->getEndDate())
                {{ $rate->getEndDate()->format('M j, Y') }}
            @else
                Present
            @endif
        </td>
        <td>{{ $rate->getRate() }}</td>
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
