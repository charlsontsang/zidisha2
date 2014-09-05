<?php

return [
    'form' => [
        'username' => 'Create Username',
        'email' => 'Email Address',
        'password' => 'Create Password',
        'firstName' => 'First Name',
        'lastName' => 'Last Name',
        'address' => 'Please enter the name of the neighborhood and street on which your home is located.',
        'addressInstructions' => 'Please enter detailed instructions of how to find your home, including your house number or plot number. If your home is not numbered, please describe how to locate it.',
        'addressInstruction' => 'Please ensure you enter a detailed enough description that a person arriving for the first time in your neighborhood can use it to find your home. Insufficient address information is the most common reason applications to join Zidisha are declined.',
        'city' => 'City or Village of Residence',
        'nationalIdNumber' => 'National ID Number',
        'phoneNumber' => 'Your Mobile Phone Number',
        'optional' => 'Optional: if you have any other phone number besides the one above, please enter it here.',
        'alternatePhoneNumber' => 'Alternate Mobile Phone Number',
        'members' => 'Please select the name of the member who referred you to Zidisha:',
        'volunteerMentorCity' => 'Please choose the town or village where you are located, or nearest to you:',
        'volunteerMentor' => 'Please choose one person from this list to serve as your Volunteer Mentor:',
        'contact' => [
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'phoneNumber' => 'Mobile Phone Number',
            'organizationTitle' => 'Name of institution and official title',
            'relationship' => 'Relationship',
        ],
        'communityLeader' => 'Community Leader',
        'communityLeaderDescription' => 'Please enter the contact information of a community leader, such as the leader of a local school, religious
    institution or other community organization, who knows you well and can recommend you for a Zidisha loan.',
        'familyMember' => 'Family Member',
        'familyMemberDescription' => 'Please enter the contact information of three family members whom we may contact as a reference',
        'neighbor' => 'Neighbor / Business Associate',
        'neighborDescription' => 'Please enter the contact information of three neighbors whom we may contact as a reference',
    ],

    'emails' => [
        'subject'=>[
            'volunteer-mentor-confirmation' => 'New assigned member :name',
        ]
    ],
    'sms' => [
        'contact-confirmation' =>
            'Dear :contactName, :borrowerName of tel. :borrowerPhoneNumber has shared
            your contacts in an application to join the Zidisha.org online lending community.
            
            We would like to confirm with you that :borrowerName
            can be trusted to repay loans. If you do not know or do not recommend
            :borrowerName, please inform us by SMS reply to this number. Thank you.'
    ]
];
