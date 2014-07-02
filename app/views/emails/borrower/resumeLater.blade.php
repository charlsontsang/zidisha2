<h1>Please click on the link to resume the borrower application.</h1>
<br/>
<hr/>
<a href="{{ route('borrower:resumeApplication', $resumeCode) }}">Resume Borrower Application</a>
<br/>
<h2>Your resume code is given bellow for your reference</h2>
<br/>
{{ $resumeCode }}
