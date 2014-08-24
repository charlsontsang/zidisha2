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
    {{ BootstrapForm::open(['action' => 'AdminLoanForgivenessController@postAllow']) }}
    
    {{ BootstrapForm::select('countryCode', $form->getCountries(), $form->getCountry()->getCountryCode(), ['label' => 'Select Country', 'id' => 'countryCode']) }}

    {{ BootstrapForm::select('loanId', $form->getLoans(), null, ['label' => 'Select Loan', 'id' => 'loanId']) }}

    {{ BootstrapForm::textarea('comment', null, ['label' => 'Comment']) }}

    {{ BootstrapForm::submit('Allow Forgiveness') }}

    {{ BootstrapForm::close() }}
</div>
@stop


@section('script-footer')
<script type="text/javascript">
    $(function () {
        var url = "{{ action('AdminLoanForgivenessController@getLoans') }}";
        $('#countryCode').on('change', function() {
            var $this = $(this),
                $loans = $('#loanId');
            $loans.attr('disabled', 'disabled');
            $.get(url + '?countryCode=' + $this.val(), function(res) {
                $loans.empty();
                $.each(res, function(key, option) {
                    $loans.append($("<option></option>")
                        .attr("value", option[0]).text(option[1]));
                });
                $loans.removeAttr('disabled');
            });
        });
    });
</script>
@stop