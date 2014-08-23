@extends('layouts.master')

@section('page-title')
Allow loan forgiveness
@stop

@section('content')
<div class="page-header">
    <h1>Allow Forgive Loans</h1>
</div>

<a href="{{route('admin:forgiven-loan:index')}}?countryCode={{ $country->getCountryCode() }}">See Forgiven Loans</a>
<hr/>

<div>
    {{ BootstrapForm::open(['route' => ['admin:post:allow-forgive-loan', $country->getId()]]) }}
    
    {{ BootstrapForm::select('countryCode', $borrowerCountries, $country->getCountryCode(), ['label' => 'Select Country', 'id' => 'countrySelect']) }}

    {{ BootstrapForm::select('loanId', $loans, null,['label' => 'Select Country']) }}

    {{ BootstrapForm::textarea('comment', null, ['label' => 'Comment']) }}

    {{ BootstrapForm::submit('Allow Forgiveness') }}

    {{ BootstrapForm::close() }}
</div>
@stop

@section('script-footer')
<script>
   $('#countrySelect').change(function(e){
       window.location.href = '{{ route('admin:allow-forgive-loan').'?countryCode=' }}' +  $("#countrySelect option:selected").attr('value');
   });
</script>
@stop

