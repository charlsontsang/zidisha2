@extends('layouts.master')

@section('page-title')
Allow loan forgiveness
@stop

@section('content')
<div class="page-header">
    <h1>Allow Forgive Loans</h1>
</div>

<a href="{{route('admin:loan-forgiveness:index')}}?countryCode={{ $country->getCountryCode() }}">See Forgiven Loans</a>
<hr/>

<div>
    {{ BootstrapForm::open(['route' => ['admin:loan-forgiveness:allow', $country->getId()]]) }}
    
    {{ BootstrapForm::select('countryCode', $borrowerCountries, $country->getCountryCode(), ['label' => 'Select Country', 'id' => 'countrySelect']) }}

    {{ BootstrapForm::select('loanId', $loans, null,['label' => 'Select Loan']) }}

    {{ BootstrapForm::textarea('comment', null, ['label' => 'Comment']) }}

    {{ BootstrapForm::submit('Allow Forgiveness') }}

    {{ BootstrapForm::close() }}
</div>
@stop

@section('script-footer')
<script>
   $('#countrySelect').change(function(e){
       window.location.href = '{{ route('admin:loan-forgiveness:allow').'?countryCode=' }}' +  $("#countrySelect option:selected").attr('value');
   });
</script>
@stop

