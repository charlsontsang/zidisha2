@extends('layouts.master')

@section('content')
    {{ BootstrapForm::open(array('controller' => 'PendingDisbursementsController@postPendingDisbursements', 'translationDomain' => 'admin.reports.pending-disbursements')) }}

    {{ BootstrapForm::select('CountryCode', $countries->toKeyValue('CountryCode', 'name')) }}

    {{ BootstrapForm::submit('Select') }}

    {{ BootstrapForm::close() }}
@stop
