<h1>Please click on this link to continue working on your application:</h1>
<br/>
<hr/>
<a href="{{ route('borrower:resumeApplication', $resumeCode) }}">Resume Application</a>
<br/>
<h2>Your application code is given below for your reference.</h2>
<br/>
{{ $resumeCode }}
