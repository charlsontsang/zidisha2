@extends('layouts.master')

@section('content')
    {{ BootstrapForm::open(array('controller' => 'BorrowerJoinController@postCountry', 'translationDomain' => 'borrower.join.select-country')) }}

    {{ BootstrapForm::select('country', $form->getCountries()->toKeyValue('id', 'name'), ['id' => $country['id'],
'name' =>$country['name']]) }}

    {{ BootstrapForm::submit('continue') }}

    {{ BootstrapForm::close() }}
    <br/>
    <br/>
    {{ link_to_route('lender:join', 'Join as lender') }}
@stop
