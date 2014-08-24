@extends('layouts.master')

@section('page-title')
Allow loan forgiveness
@stop

@section('content')
<div class="page-header">
    <h1>Allow Loan Forgiveness</h1>
</div>

<a href="{{route('admin:loan-forgiveness:index', $form->getCountry()->getCountryCode()) }}">Back to overview</a>
<hr/>

<div>
    {{ BootstrapForm::open(['route' => ['admin:loan-forgiveness:allow']]) }}
    
    {{ BootstrapForm::select('countryCode', $form->getCountries(), $form->getCountry()->getCountryCode(), ['label' => 'Select Country', 'id' => 'countrySelect']) }}

    {{ BootstrapForm::select('loanId', $form->getLoans(), null, ['label' => 'Select Loan']) }}

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

