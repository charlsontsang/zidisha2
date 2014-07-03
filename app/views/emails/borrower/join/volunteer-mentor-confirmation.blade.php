Dear {{ $borrower->getVolunteerMentor()->getBorrowerVolunteer()->getName() }},
<br/><br/>
{{ $borrower->getName() }} has applied to join Zidisha and has selected you as a Volunteer Mentor.
We encourage you to review
{{ $borrower->getName() }}'s profile here:
<a href="{{ route('borrower:public-profile', $borrower->getUser()->getUsername()) }}">View</a>
<br/><br/>
If you have any concerns about the information {{ $borrower->getVolunteerMentor()->getBorrowerVolunteer()->getName() }} has provided, please let us know by replying to this email.<br/><br/>
Thank you,
<br/><br/>
The Zidisha Team
