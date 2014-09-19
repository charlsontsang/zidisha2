@extends('layouts.master')

@section('page-title')
Forgiven Loans
@stop

@section('content')
<div class="page-header">
    <h1>
        Forgiven Loans
    </h1>
</div>

<ul class="nav nav-tabs" role="tablist">
    @foreach($borrowerCountries as $borrowerCountry)
        <li class="{{ $borrowerCountry->getCountryCode() == $countryCode ? 'active' : '' }}">
            <a href="{{ route('admin:loan-forgiveness:index', $borrowerCountry->getCountryCode()) }}">
                {{ $borrowerCountry->getName() }}
            </a>
        </li>
    @endforeach
</ul>

<table class="table table-striped" id="forgiven-loans">
    <thead>
        <tr>
            <th>Borrower</th>
            <th>Comment</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($forgivenessLoans as $loan)
            <tr>
                <td>
                     {{ $loan->getBorrower()->getFirstName() }} {{ $loan->getBorrower()->getLastName() }}
                </td>
                <td>
                   <p>
                        {{{ $loan->getComment() }}}
                   </p> 
                </td>
                <td>
                    {{ $loan->getCreatedAt()->format('M j, Y') }}
                </td>
            </tr>
        @endforeach    
    </tbody>
</table>

{{ BootstrapHtml::paginator($forgivenessLoans)->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#forgiven-loans').dataTable({
            'searching': true
        });
    });
</script>
@stop
