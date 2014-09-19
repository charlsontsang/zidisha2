@extends('layouts.master')

@section('page-title')
{{ $borrower->getName() }}
@stop

@section('content')
<div class="page-header">
    <h1>{{ $borrower->getName() }}
        <span class="pull-right">
            <a href="{{ route('admin:borrower:edit', $borrower->getId()) }}"> <i class="fa fa-pencil-square-o fa-lg"></i></a>
        </span>
    </h1>
</div>

<div class="row">
    <div class="col-sm-4 pull-right mobile-padding">
        @if(!$borrower->getUploads()->isEmpty())
        <div>      
            @foreach($borrower->getUploads() as $upload)
                @if($upload->isImage())
                <a href="{{ $upload->getImageUrl('small-profile-picture') }}">
                    <img src="{{ $upload->getImageUrl('small-profile-picture') }}" width="100%" />
                </a>
                @else
                <div class="well">
                    <a href="{{  $upload->getFileUrl()  }}">{{ $upload->getFilename() }}</a>
                </div>
                @endif
            @endforeach
        </div>
        @else
        <img width="200" height="200" src="{{ $borrower->getUser()->getProfilePictureUrl() }}">
        @endif
    </div>

    <div class="col-sm-8">
        <div class="loan-section">
            <div class="loan-section-title">
                <span class="text-light">Loan History</span>
            </div>
            <div class="loan-section-content">
                @if($loans)
                    @foreach($loans as $loan)
                    {{ $loan->getAmount() }}: TODO (show dates)&nbsp;&nbsp;&nbsp;<a href="{{ route('loan:index', $loan->getId()) }}">View Loan Profile</a>
                    <br/>
                    @endforeach
                @else
                    No loans
                @endif
            </div>
        </div>

        <hr/>

        <div class="loan-section">
            <div class="loan-section-title">
                <span class="text-light">Account</span>
            </div>
            <div class="loan-section-content">
                Registration Date: <strong>{{ $borrower->getUser()->getCreatedAt()->format('M d, Y') }}</strong>
                <br/>
                Account Status: <strong>{{ $borrower->getActivationStatus() }}</strong>
            </div>
        </div>

        <hr/>

        <div class="loan-section">
            <div class="loan-section-title">
                <span class="text-light">Contact Information</span>
            </div>
            <div class="loan-section-content">
                Email: <strong>{{ $borrower->getUser()->getEmail() }}</strong>
                <br/>
                Phone: <strong>{{ BootstrapHtml::number($borrower->getProfile()->getPhoneNumber(), $borrower->getCountry()->getCountryCode()) }}</strong>
                <br/>
                @if (!empty($borrower->getProfile()->getAlternatePhoneNumber()))
                    Alternate Phone: <strong>{{ BootstrapHtml::number($borrower->getProfile()->getAlternatePhoneNumber(), $borrower->getCountry()->getCountryCode()) }}</strong>
                @endif
            </div>
        </div>

        <hr/>

        <div class="loan-section">
            <div class="loan-section-title">
                <span class="text-light">Location</span>
            </div>
            <div class="loan-section-content">
                Address:
                <br/>
                <strong>{{ $borrower->getProfile()->getAddress() }}</strong>
                <br/>
                <strong>{{ $borrower->getProfile()->getAddressInstructions() }}</strong>
                <br/>
                <strong>{{ $borrower->getProfile()->getCity() }}, {{ $borrower->getCountry()->getName() }}</strong>
                <br/>
                National ID: <strong>{{ $borrower->getProfile()->getNationalIdNumber() }}</strong>
            </div>
        </div>

        <hr/>

        <div class="loan-section">

            <div class="loan-section-title">
                <span class="text-light">References</span>
            </div>
            <div class="loan-section-content">
                Community Leader: <strong>{{ $personalInformation['communityLeader_firstName'] }} {{ $personalInformation['communityLeader_lastName'] }}</strong>
                <br/>
                Title: <strong>{{ $personalInformation['communityLeader_description'] }}</strong>
                <br/>
                Phone Number: <strong>{{ $personalInformation['communityLeader_phoneNumber'] }}</strong>
                <br/><br/>
                Family Member: <strong>{{ $personalInformation['familyMember_1_firstName'] }} {{ $personalInformation['familyMember_1_lastName'] }}</strong>
                <br/>
                Relationship: <strong>{{ $personalInformation['familyMember_1_description'] }}</strong>
                <br/>
                Phone Number: <strong>{{ $personalInformation['familyMember_1_phoneNumber'] }}</strong>
                <br/><br/>
                Family Member: <strong>{{ $personalInformation['familyMember_2_firstName'] }} {{ $personalInformation['familyMember_2_lastName'] }}</strong>
                <br/>
                Relationship: <strong>{{ $personalInformation['familyMember_2_description'] }}</strong>
                <br/>
                Phone Number: <strong>{{ $personalInformation['familyMember_2_phoneNumber'] }}</strong>
                <br/><br/>
                Family Member: <strong>{{ $personalInformation['familyMember_3_firstName'] }} {{ $personalInformation['familyMember_3_lastName'] }}</strong>
                <br/>
                Relationship: <strong>{{ $personalInformation['familyMember_3_description'] }}</strong>
                <br/>
                Phone Number: <strong>{{ $personalInformation['familyMember_3_phoneNumber'] }}</strong>
                <br/><br/>
                Neighbor: <strong>{{ $personalInformation['neighbor_1_firstName'] }} {{ $personalInformation['neighbor_1_lastName'] }}</strong>
                <br/>
                Relationship: <strong>{{ $personalInformation['neighbor_1_description'] }}</strong>
                <br/>
                Phone Number: <strong>{{ $personalInformation['neighbor_1_phoneNumber'] }}</strong>
                <br/><br/>
                Neighbor: <strong>{{ $personalInformation['neighbor_2_firstName'] }} {{ $personalInformation['neighbor_2_lastName'] }}</strong>
                <br/>
                Relationship: <strong>{{ $personalInformation['neighbor_2_description'] }}</strong>
                <br/>
                Phone Number: <strong>{{ $personalInformation['neighbor_2_phoneNumber'] }}</strong>
                <br/><br/>
                Neighbor: <strong>{{ $personalInformation['neighbor_3_firstName'] }} {{ $personalInformation['neighbor_3_lastName'] }}</strong>
                <br/>
                Relationship: <strong>{{ $personalInformation['neighbor_3_description'] }}</strong>
                <br/>
                Phone Number: <strong>{{ $personalInformation['neighbor_3_phoneNumber'] }}</strong>
                <br/><br/>
            </div>
        </div>

        <hr/>

        <div class="loan-section">

            <div class="loan-section-title">
                <span class="text-light">About</span>
            </div>
            <div class="loan-section-content">
                {{ $borrower->getProfile()->getAboutMe() }}
                <br/>
                {{ $borrower->getProfile()->getAboutBusiness() }}
            </div>
        </div>
    </div>
</div>

@stop
