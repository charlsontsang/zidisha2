@extends('layouts.master')

@section('page-title')
Manage Volunteer Mentors
@stop

@section('content')
<div class="page-header">
    <h1>
        Manage Volunteer Mentors
    </h1>
</div>

<div class="panel panel-info">
    <div class="panel-heading">
        {{ BootstrapForm::open(array('route' => 'admin:volunteer-mentors')) }}
        {{ BootstrapForm::populate($form) }}

        {{ BootstrapForm::select('country', $form->getCountries(), Request::query('country'), ['label' => false, 'id' => 'country', 'style' => 'width: 38%']) }}

        {{ BootstrapForm::close() }}

        {{ BootstrapForm::open([
            'action' => ['AdminController@getVolunteerMentors', 'country' => Request::query('country')],
            'method' => 'get',
            'class' => 'form-inline',
        ]) }}

        {{ BootstrapForm::close() }}
    </div>
    <div class="panel-body">
    @if($paginator)
        <table class="table table-striped" id="mentors">
            <thead>
            <tr>
                <th>Volunteer Mentor</th>
                <th>Number of Assigned Members</th>
                <th>Assigned Member Status</th>
                <th>Mentor Repayment Status</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>
            @foreach($paginator as $borrower)
            <tr>
                <td><a href="{{ route('admin:borrower', $borrower->getId()) }}">{{
                        $borrower->getName() }}</a>
                    <p>{{ BootstrapHtml::number($borrower->getProfile()->getPhoneNumber(), $borrower->getCountry()->getCountryCode()) }}</p>
                    <p>{{ $borrower->getUser()->getEmail() }}</p>
                    <p>{{ $borrower->getProfile()->getCity() }}, {{ $borrower->getCountry()->getName() }}</p>
                </td>
                <td>
                    {{ $menteeCounts[$borrower->getId()] }}

                    @if(Auth::getUser()->isAdmin())
                        <br/><br/>
                        <a href="{{ route('admin:remove:volunteer-mentor', $borrower->getId()) }}">Remove mentor status</a>
                    @endif
                </td>
                <td>
                    <a href="#" id="toggle-members" data-toggle="collapse" data-target="#members" data-toggle-text="Hide members">
                        View members
                    </a>
                    <div id="members" class="collapse">
                        <br/>
                        <table class="table" id="member-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Mentor Comment</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach($assignedMembers as $assignedMember)
                                        @if($assignedMember->getVolunteerMentorId() == $borrower->getId())
                                            <tr>
                                                <td><a href="{{ route('admin:borrower', $assignedMember->getId()) }}">{{
                                                    $assignedMember->getName() }}</a></td>
                                                <td>
                                                {{ $borrowerService->printLoanInArrears($assignedMember) }}
                                                </td>
                                                <td>
                                                    @if($borrowerService->hasVMComment($borrower, $assignedMember))
                                                        <a href="{{ route('admin:borrower:personal-information', $assignedMember->getUser()->getUsername()) }}">
                                                        View Comment
                                                        </a>
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
                    </div>
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

                    <a href="#" class="add-note-toggle">Add note</a>
                    
                    <div id="add-note" class="collapse">  
                        {{ BootstrapForm::open(array('action' => 'AdminController@postVmNote')) }}
                            {{ BootstrapForm::textarea('note', null, ['rows' => '5', 'label' => false]) }}
                            {{ BootstrapForm::hidden('borrowerId', $borrower->getId()) }}
                            {{ BootstrapForm::submit('Save') }}
                        {{ BootstrapForm::close() }}
                    </div>

                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No volunteer mentors are active in this country.</p>
    @endif
    </div>
</div>
{{ BootstrapHtml::paginator($paginator)->appends(['country' => Request::query('country')])->links() }}
@stop

@section('script-footer')
<script type="text/javascript">
    $(document).ready(function () {
        $('#mentors').dataTable({
            'searching': true,
            'info': true
        });
        $('#member-table').dataTable();
    });
    $(function() {
        $('#country').change(function() {
            this.form.submit();
        $('.members-toggle').click(function () {
            $("#members").collapse('toggle');
            return false;
        });
        $('.add-note-toggle').click(function () {
            $("#add-note").collapse('toggle');
            return false;
        });
    });
});
</script>
@stop
