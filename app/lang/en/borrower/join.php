<?php

return [
    'form'   => [
        'title'                      => 'Join Zidisha',
        'country'                    => 'Country',
        'next'                       => 'Next step',
        'resume-code'                => 'Enter code to resume your application',
        'resume-submit'              => 'Resume your application',
        'facebook-intro'             => "In order to verify your online identity, please click on the ':buttonText' button to link your Facebook account.",
        'facebook-note'              => 'Please make sure you are signed into your own Facebook account before linking. If you link a Facebook account that does not belong to you, your application to join Zidisha may be permanently declined. A link to your public Facebook page will be displayed to lenders in your Zidisha loan profile page.',
        'facebook-button'            => 'Verify with Facebook',
        'facebook-skip'              => 'Skip',
        'username'                   => 'Create Username',
        'email'                      => 'Email Address',
        'password'                   => 'Create Password',
        'preferred-loan-amount'      => 'How much would you like to borrow?',
        'preferred-interest-rate'    => 'What interest rate would you prefer?',
        'preferred-repayment-amount' => 'How much would you like to repay each week?',
        'business-category-id'       => 'Please select the category that best describes your business.',
        'business-years'             => 'How many years have you been in business?',
        'loan-usage'                 => 'How do you plan to use the loan?',
        'birth-date'                 => 'Date of birth',
        'first-name'                 => 'First Name',
        'last-name'                  => 'Last Name',
        'address'                    => 'Please enter the name of the neighborhood and street on which your home is located.',
        'address-instructions'       => 'Please enter detailed instructions of how to find your home, including your house number or plot number. If your home is not numbered, please describe how to locate it.',
        'address-instruction'        => 'Please ensure you enter a detailed enough description that a person arriving for the first time in your neighborhood can use it to find your home. Insufficient address information is the most common reason applications to join Zidisha are declined.',
        'city'                       => 'City or Village of Residence',
        'national-id-number'         => 'National ID Number',
        'phone-number'               => 'Your Mobile Phone Number',
        'alternate-phone-number-description' => 'Optional: if you have any other phone number besides the one above, please enter it here.',
        'alternate-phone-number'     => 'Alternate Mobile Phone Number',
        'volunteer-mentor-city'      => 'Please choose the town or village where you are located, or nearest to you:',
        'volunteer-mentor'           => 'Please choose one person from this list to serve as your Volunteer Mentor:',

        'contact'                    => [
            'first-name'         => 'First Name',
            'last-name'          => 'Last Name',
            'phone-number'       => 'Mobile Phone Number',
            'organization-title' => 'Name of institution and official title',
            'relationship'       => 'Relationship',
        ],

        'community-leader'            => 'Community Leader',
        'community-leader-description' => 'Please enter the contact information of a community leader, such as the leader of a local school, religious
    institution or other community organization, who knows you well and can recommend you for a Zidisha loan.',
        'family-member'               => 'Family Member',
        'family-member-description'    => 'Please enter the contact information of three family members whom we may contact as a reference',
        'neighbor'                   => 'Neighbor / Business Associate',
        'neighbor-description'        => 'Please enter the contact information of three neighbors whom we may contact as a reference',
        
        'terms-and-condition' => [
            'legend'       => 'Terms of use',
            'confirmation' => 'I have read and agree to the',
            'link'         => 'Zidisha Loan Contract',
        ],
        
        'submit'              => 'Submit final application',
        'save-later'          => 'Save and Complete Later',
        'disconnect-facebook' => 'Disconnect facebook account',
    ],

    'emails' => [
        'subject' => [
            'confirmation'                  => 'Zidisha application submitted',
            'volunteer-mentor-confirmation' => 'New member: :name',

        ]
    ],

    'sms'    => [
        'contact-confirmation' =>
            'Dear :contactName, :borrowerName of tel. :borrowerPhoneNumber has shared
            your contacts in an application to join the Zidisha.org online lending community.

            If you know and recommend :borrowerName as a trustworthy person, you do not need to respond. If you do not know or do not recommend
            :borrowerName, please inform us by SMS reply to this number. Thank you.'
    ]
];
