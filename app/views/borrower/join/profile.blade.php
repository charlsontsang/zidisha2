@extends('layouts.master')

@section('content')
{{ BootstrapForm::open(array('controller' => 'BorrowerJoinController@postProfile', 'translationDomain' => 'borrower.join.profile')) }}

{{ BootstrapForm::populate($form) }}
<br><br>
<p>CREATE ACCOUNT</p>
{{ BootstrapForm::text('username') }}
{{ BootstrapForm::password('password') }}
{{ BootstrapForm::text('email') }}

<br><br>
<p>CONTACT INFORMATION</p>
{{ BootstrapForm::text('first_name') }}
{{ BootstrapForm::text('last_name') }}
{{ BootstrapForm::label('Please enter the name of the neighborhood and street on which your home is located.') }}
{{ BootstrapForm::text('address') }}
<br>
{{ BootstrapForm::label('Please enter detailed instructions of how to find your home, including your house number or plot number. If your home is not numbered, please describe how to locate it.') }}
<br><br>
{{ BootstrapForm::label('Please ensure you enter a detailed enough description that a person arriving for the first time in your neighborhood can use it to find your home. Insufficient address information is the most common reason applications to join Zidisha are declined.') }}
{{ BootstrapForm::textArea('address_instruction') }}
{{ BootstrapForm::text('village') }}
{{ BootstrapForm::text('national_id_number') }}
{{ BootstrapForm::text('phone_number') }}
{{ BootstrapForm::label('Optional: if you have any other phone number besides the one above, please enter it here.') }}
{{ BootstrapForm::text('alternate_phone_number') }}

<br><br>
<p>REFERENCES</p>
{{ BootstrapForm::label('Please select the name of the member who referred you to Zidisha:') }}
{{ BootstrapForm::select('members') }}
{{ BootstrapForm::label('Please choose the town or village where you are located, or nearest to you:') }}
{{ BootstrapForm::select('town') }}
{{ BootstrapForm::label('Please choose one person from this list to serve as your Volunteer Mentor:') }}
{{ BootstrapForm::select('mentor') }}

<br><br>
<p>Please enter the contact information of a community leader, such as the leader of a local school, religious
    institution or other community organization, who knows you well and can recommend you for a Zidisha loan.</p>

{{ BootstrapForm::submit('submit') }} -
{{ BootstrapForm::submit('save_later') }} -
{{ BootstrapForm::submit('diconnect_facebook_account') }}


{{ BootstrapForm::close() }}
<br/>
<br/>
{{ link_to_route('lender:join', 'Join as lender') }}
@stop
