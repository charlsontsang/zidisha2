@extends('layouts.master')

@section('page-title')
{{ $borrower->getName() }}
@stop

@section('content')
<div class="page-header">
    <h1>Borrower Details <span class="pull-right"><a href="{{ route('admin:borrower:edit', $borrower->getId()) }}"> <i class="fa fa-pencil-square-o fa-lg"></i></a></span></h1>
</div>

<div class="row">
    <div class="col-xs-8">

        <div>
            <h3>Loans Raised</h3>
            @if($loans)
                @foreach($loans as $loan)
                {{ $loan->getAmount() }} - TODO (show dates) - <a href="{{ route('loan:index', $loan->getId()) }}">View Loan Profile</a>
                <br/>
                @endforeach
            @else
                NO LOANS
            @endif
        </div>

        <div>
            <h2>Borrower Profile</h2>
            <dl class="dl-horizontal">
                <dt>Applicant Name</dt>
                <dd>{{ $borrower->getName() }}</dd>

                <hr/>

                <dt>About Me</dt>
                <dd>{{ $borrower->getProfile()->getAboutMe() }}</dd>

                <hr/>

                <dt>About Business</dt>
                <dd>{{ $borrower->getProfile()->getAboutBusiness() }}</dd>

                <hr/>

                <dt>Address</dt>
                <dd>{{ $borrower->getProfile()->getAddress() }}</dd>

                <hr/>

                <dt>City</dt>
                <dd>{{ $borrower->getProfile()->getCity() }}</dd>

                <hr/>


                <dt>Country</dt>
                <dd>{{ $borrower->getCountry()->getName() }}</dd>

                <hr/>

                <dt>National Id Number</dt>
                <dd>{{ $borrower->getProfile()->getNationalIdNumber() }}</dd>

                <hr/>

                <dt>Telephone</dt>
                <dd>{{ $borrower->getProfile()->getPhoneNumber() }}</dd>

                <hr/>

                <dt>Alternate Phone Number</dt>
                <dd>{{ $borrower->getProfile()->getAlternatePhoneNumber() }}</dd>

                <hr/>


                <dt>Date of Joining</dt>
                <dd>{{ $borrower->getUser()->getCreatedAt()->format('M d, Y') }}</dd>

                <hr/>

                <dt>Application Status</dt>
                <dd>{{ $borrower->getActivationStatus() }}</dd>

                <hr/>

                <dt>Email</dt>
                <dd>{{ $borrower->getUser()->getEmail() }}</dd>

            </dl>
        </div>

        <div>
            <h2>Borrower Personal Information</h2>

            <dl class="dl-horizontal">
                <hr/>
                <h4>Community Leader</h4>
                <dt>First Name</dt>
                <dd>{{ $personalInformation['communityLeader_firstName'] }} </dd>

                <dt>Last Name</dt>
                <dd>{{ $personalInformation['communityLeader_lastName'] }}</dd>

                <dt>Phone Number</dt>
                <dd>{{ $personalInformation['communityLeader_phoneNumber'] }}</dd>

                <dt>Description</dt>
                <dd>{{ $personalInformation['communityLeader_description'] }}</dd>

                <hr/>

                <h4>Family Members</h4>
                <hr/>
                <h5>Family Member 1</h5>

                <dt>First Name</dt>
                <dd>{{ $personalInformation['familyMember_1_firstName'] }} </dd>

                <dt>Last Name</dt>
                <dd>{{ $personalInformation['familyMember_1_lastName'] }}</dd>

                <dt>Phone Number</dt>
                <dd>{{ $personalInformation['familyMember_1_phoneNumber'] }}</dd>

                <dt>Description</dt>
                <dd>{{ $personalInformation['familyMember_1_description'] }}</dd>

                <hr/>

                <h5>Family Member 2</h5>

                <dt>First Name</dt>
                <dd>{{ $personalInformation['familyMember_2_firstName'] }} </dd>

                <dt>Last Name</dt>
                <dd>{{ $personalInformation['familyMember_2_lastName'] }}</dd>

                <dt>Phone Number</dt>
                <dd>{{ $personalInformation['familyMember_2_phoneNumber'] }}</dd>

                <dt>Description</dt>
                <dd>{{ $personalInformation['familyMember_2_description'] }}</dd>

                <hr/>

                <h5>Family Member 3</h5>

                <dt>First Name</dt>
                <dd>{{ $personalInformation['familyMember_3_firstName'] }} </dd>

                <dt>Last Name</dt>
                <dd>{{ $personalInformation['familyMember_3_lastName'] }}</dd>

                <dt>Phone Number</dt>
                <dd>{{ $personalInformation['familyMember_3_phoneNumber'] }}</dd>

                <dt>Description</dt>
                <dd>{{ $personalInformation['familyMember_3_description'] }}</dd>

                <hr/>

                <h4>Neighbors</h4>
                <hr/>
                <h5>Neighbor 1</h5>

                <dt>First Name</dt>
                <dd>{{ $personalInformation['neighbor_1_firstName'] }} </dd>

                <dt>Last Name</dt>
                <dd>{{ $personalInformation['neighbor_1_lastName'] }}</dd>

                <dt>Phone Number</dt>
                <dd>{{ $personalInformation['neighbor_1_phoneNumber'] }}</dd>

                <dt>Description</dt>
                <dd>{{ $personalInformation['neighbor_1_description'] }}</dd>

                <hr/>

                <h5>Neighbor 2</h5>

                <dt>First Name</dt>
                <dd>{{ $personalInformation['neighbor_2_firstName'] }} </dd>

                <dt>Last Name</dt>
                <dd>{{ $personalInformation['neighbor_2_lastName'] }}</dd>

                <dt>Phone Number</dt>
                <dd>{{ $personalInformation['neighbor_2_phoneNumber'] }}</dd>

                <dt>Description</dt>
                <dd>{{ $personalInformation['neighbor_2_description'] }}</dd>

                <hr/>

                <h5>Neighbor 3</h5>

                <dt>First Name</dt>
                <dd>{{ $personalInformation['neighbor_3_firstName'] }} </dd>

                <dt>Last Name</dt>
                <dd>{{ $personalInformation['neighbor_3_lastName'] }}</dd>

                <dt>Phone Number</dt>
                <dd>{{ $personalInformation['neighbor_3_phoneNumber'] }}</dd>

                <dt>Description</dt>
                <dd>{{ $personalInformation['neighbor_3_description'] }}</dd>

            </dl>
        </div>
    </div>

    <div class="col-xs-4">
        <img width="200" height="200" src="{{ $borrower->getUser()->getProfilePictureUrl() }}">
    </div>
</div>

@if(!$borrower->getUploads()->isEmpty())
<h4>Borrower Pictures</h4>
<div>
    @foreach($borrower->getUploads() as $upload)
    @if($upload->isImage())
    <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
        <img src="{{ $upload->getImageUrl('small-profile-picture') }}" alt=""/>
    </a>
    @else
    <div class="well">
        <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
    </div>
    @endif
    @endforeach
</div>
@endif
@stop
