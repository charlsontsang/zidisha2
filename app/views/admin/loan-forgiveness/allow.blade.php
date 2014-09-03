@extends('layouts.side-menu')

@section('page-title')
Enable Loan Forgiveness
@stop

@section('menu-title')
Quick Links
@stop

@section('menu-links')
@include('partials.nav-links.staff-links')
@stop

@section('page-content')
<a href="{{route('admin:loan-forgiveness:index', $form->getCountry()->getCountryCode()) }}">Back to overview</a>
<hr/>

{{ BootstrapForm::open(['action' => 'AdminLoanForgivenessController@postAllow']) }}

{{ BootstrapForm::select('countryCode', $form->getCountries(), $form->getCountry()->getCountryCode(), ['label' => 'Select Country', 'id' => 'countryCode']) }}

{{ BootstrapForm::select('loanId', $form->getLoans(), null, ['label' => 'Select Loan', 'id' => 'loanId']) }}

{{ BootstrapForm::textarea('comment', null, ['label' => 'Comment']) }}

{{ BootstrapForm::submit('Allow Forgiveness') }}

{{ BootstrapForm::close() }}
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