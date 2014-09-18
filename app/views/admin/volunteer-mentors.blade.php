@extends('layouts.master')

@section('content')
<div class="page-header">
    <h1>
        Manage Volunteer Mentors
    </h1>
</div>

{{ BootstrapForm::open(array('route' => 'admin:volunteer-mentors', 'translationDomain' => 'volunteer-mentors', 'method' => 'get')) }}
{{ BootstrapForm::populate($form) }}

{{ BootstrapForm::select('country', $form->getCountries(), Request::query('country')) }}
{{ BootstrapForm::submit('Search') }}

{{ BootstrapForm::close() }}

{{ BootstrapForm::open([
    'action' => ['AdminController@getVolunteerMentors', 'country' => Request::query('country'), 'search' => Request::query('search')],
    'method' => 'get',
    'class' => 'form-inline',
]) }}

{{ BootstrapForm::close() }}

@if($paginator)
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Borrower</th>
            <th>City / Village</th>
            <th>Number of Assigned Members</th>
            <th>Assigned Member Status</th>
            <th>VM Repayment Status</th>
            <th>Notes</th>
            <th>Volunteer Mentor Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($paginator as $borrower)
        <tr>
            <td><a href="{{ route('admin:borrower', $borrower->getId()) }}">{{
                    $borrower->getName() }}</a>
                <p>{{ $borrower->getProfile()->getPhoneNumber() }}</p>
                <p>{{ $borrower->getUser()->getEmail() }}</p>
            </td>
            <td>{{ $borrower->getProfile()->getCity() }}</td>
            <td>{{ $menteeCounts[$borrower->getId()] }}</td>
            <td>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Assigned Member Name</th>
                        <th>Assigned Member Status</th>
                        <th>Assigned Member Comment Posted</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedMembers as $assignedMember)
                            @if($assignedMember->getVolunteerMentorId() == $borrower->getId())
                                <tr>
                                    <td><a href="{{ route('admin:borrower', $assignedMember->getId()) }}">{{
                                                        $assignedMember->getName() }}</a></td>
                                    <td>
                                    TODO (Activate date)
                                    {{ $borrowerService->printLoanInArrears($assignedMember) }}
                                    </td>
                                    <td>
                                        @if($borrowerService->hasVMComment($borrower, $assignedMember))
                                            <a href="{{ route('borrower:public-profile', $assignedMember->getUser()->getUsername()) }}">View Comment</a>
                                        @else
                                            No Comment
                                        @endif
                                    </td>
                                </tr>
                                <?php unset($assignedMember) ?>
                            @endif
                @endforeach
                </tbody>
                </table>
            </td>
            <td> {{ $borrowerService->printLoanInArrears($borrower) }} </td>
            <td>
                @if(isset($adminNotes[$borrower->getId()]))
                    <ul>
                        @foreach($adminNotes[$borrower->getId()] as $adminNote)
                            <li>
                                <span class="text-muted">
                                    {{ $adminNote->getCreatedAt('M j, Y') }} by {{ $adminNote->getUser()->getUserName() }}
                                </span>
                                <p> {{ $adminNote->getNote() }} </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <br/>
                {{ BootstrapForm::open(array('action' => 'AdminController@postVmNote')) }}
                    {{ BootstrapForm::textarea('note', null, ['rows' => '5', 'label' => false]) }}
                    {{ BootstrapForm::hidden('borrowerId', $borrower->getId()) }}
                    {{ BootstrapForm::submit('Submit') }}
                {{ BootstrapForm::close() }}
            </td>
            <td>
                <a href="{{ route('admin:remove:volunteer-mentor', $borrower->getId()) }}">Remove Volunteer Mentor</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endif
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country'), 'search' => Request::query('search')])->links() }}
@stop
