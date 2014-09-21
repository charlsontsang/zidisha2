<?php

return [
    'form'   => [
        'title'                      => 'Application for Zidisha Membership',
        'online-identity'            => 'Online Identity',
        'create-account'             => 'Create Account',
        'contact-info'               => 'Contact Information',
        'mentor-title'               => 'Choose A Mentor',
        'cl-title'                   => 'Community Leader Reference',
        'family-title'               => 'Family References',
        'neighbor-title'             => 'Other References',
        'more-info'                  => 'Loan Information',
        'country'                    => 'Country',
        'next'                       => 'Next step',
        'resume-code'                => 'Enter code to resume your application',
        'resume-submit'              => 'Resume your application',
        'facebook-intro'             => "In order to verify your online identity, please click on the ':buttonText' button to link your Facebook account.",
        'facebook-note'              => 'Please make sure you are signed into your own Facebook account before linking. If you link a Facebook account that does not belong to you, your application to join Zidisha may be permanently declined. A link to your public Facebook page will be displayed to lenders in your Zidisha loan profile page.',
        'facebook-button'            => 'Verify with Facebook',
        'facebook-skip'              => 'I do not have a Facebook account.',
        'username'                   => 'Create Username',
        'email'                      => 'Email Address',
        'password'                   => 'Create Password',
        'preferred-loan-amount'      => 'How much would you like to borrow?',
        'preferred-interest-rate'    => 'What interest rate would you prefer?',
        'preferred-repayment-amount' => 'How much would you like to repay each week?',
        'business-category-id'       => 'Please select the category that best describes your business.',
        'business-years'             => 'How many years have you been in business?',
        'loan-usage'                 => 'How do you plan to use the loan?',
        'birth-date'                 => 'Please enter your date of birth.',
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
        'family-member-description'    => 'Please enter the contact information of three family members whom we may contact as a reference.',
        'neighbor'                   => 'Neighbor / Business Associate',
        'neighbor-description'        => 'Please enter the contact information of three neighbors whom we may contact as a reference.',
        
        'terms-and-condition' => [
            'legend'       => 'Legal Contract',
            'confirmation' => 'I have read and agree to the',
            'title'         => 'Zidisha Loan Contract',
            'body'         => '
<ol>
<li type="I"><b>Loan Agreement</b><p>If the loan requested in this form is granted, I agree to repay the loan on time and in full according to the interest rate, repayment period, grace period, and other terms specified in this form.</p>
<li type="I"><b>Disclosure of Information</b>
      <ol>
        <li type="a"><p> In connection with this application and/or maintaining a credit facility with Zidisha.org, Zidisha and its authorized agents may carry out credit checks with a credit reference agency.  In the event of the account going into default, my name and transaction details will be recorded with the credit reference agency.  This information may be used by other banks and financial institutions in assessing applications for credit by me, members of my household, supplementary account holders and members of their households and for occasional debt tracing and fraud prevention purposes.</p>
        <li type="a"><p>I agree that the financial institutions with which I have held loans or credit facilities may disclose details relating to my loan or credit facilities to any third party including credit reference agencies, if in the financial institution’s opinion such disclosure is necessary for the purposes of evaluating any application made to the financial institution or such third party, maintaining my Account with the financial institution or other purpose as the financial institution shall deem appropriate.</p>
        <li type="a"><p>  I agree that the financial institution may disclose details relating to my loan or credit account including details of my default in servicing my loan or credit account to any third party including credit reference agencies for the purpose of evaluating my credit worthiness or for any other lawful purpose.</p>
     </ol> 
<li type="I"><b>Publicity</b><p>I agree that Zidisha.org may publicize all information, including pictures, provided by me on this form and in any future communications with Zidisha, as well as all information relating to my credit history provided by third parties, on the Internet for the purposes of loan application evaluation, fundraising and general publicity.
</p>
</ol>',
            'please-agree' => 'Please confirm acceptance of the Zidisha Loan Contract to continue.'
        ],
        
        'submit'              => 'Submit Final Application',
        'save-later'          => 'Save and Complete Later',
        'disconnect-facebook' => 'Disconnect Facebook account',
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
