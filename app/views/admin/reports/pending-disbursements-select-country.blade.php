@extends('layouts.master')

@section('content')
    {{ BootstrapForm::open(array('controller' => 'AdminReportsController@postPendingDisbursements', 'translationDomain' => 'admin.reports.pending-disbursements')) }}

    {{ BootstrapForm::select('country', $countries->toKeyValue('CountryCode', 'name')) }}

    {{ BootstrapForm::submit('Select') }}

    {{ BootstrapForm::close() }}
@stop
