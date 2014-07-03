Dear {{ $contact->getName() }},
{{ $contact->getBorrower()->getName() }} of tel. {{ $contact->getPhoneNumber() }} has shared
your contacts in an application to join the Zidisha.org online lending community.

We would like to confirm with you that {{ $contact->getBorrower()->getName() }}
can be trusted to repay loans. If you do not know or do not recommend
{{ $contact->getBorrower()->getName() }}, please inform us by SMS reply to this number. Thank you.