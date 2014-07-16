@extends('layouts.master')

@section('content')
    {{ BootstrapForm::open(array('controller' => 'PendingDisbursementsController@postPendingDisbursements', 'translationDomain' => 'admin.reports.pending-disbursements')) }}

    {{ BootstrapForm::select('countryCode', $countries->toKeyValue('countryCode', 'name')) }}

    {{ BootstrapForm::submit('Select') }}

    {{ BootstrapForm::close() }}
@stop
