<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>{{ \Lang::get('borrower.mails.password-reset.subject') }}</h2>

		<div>
		    {{ \Lang::get('borrower.mails.password-reset.body', ['formLink' => URL::to('password/reset', array($token)), 'expireTime' => onfig::get('auth.reminder.expire', 60)]) }}
		</div>
	</body>
</html>
