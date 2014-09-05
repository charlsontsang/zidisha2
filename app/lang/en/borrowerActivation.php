<?php

return [
    'feedback' => [
        'borrowerEmail' => 'Recipient Email',
        'cc' => 'CC',
        'replyTo' => 'Reply To',
        'subject' => 'Subject',
        'message' => 'Message',
        'message-description' => 'Please modify the default text as necessary to indicate what is needed to complete the application,
                                  and add your name to the signature line. You may change the language in the footer of this page
                                  to display the default message in French or Indonesian.',
        'senderName' => 'Enter your name here',
        'sender' => 'Sender',
        'send' => 'Send',
        'default-subject' => 'Your Zidisha Application', // TODO should be in another translation file
        'default-message' =>
'Dear :borrowerName,

Thank you for your application to join Zidisha.  We will need the following information in order to complete your application:

A precise residential address, including house number or plot number and detailed directions to your home.

Please add this information directly to your profile by logging into your member account and using the "Edit Profile" page, then resubmit the profile to Zidisha.

Once again thank you for your application to join Zidisha.

Best regards,

Zidisha Team',
    ],
    'email' => [
        'declined' => [
            'subject' => 'Message from Zidisha',
        ]
    ]
];
