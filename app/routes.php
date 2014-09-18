<?php
$languages = ['fr', 'in'];
$locale = Request::segment(1);
if (in_array($locale, $languages)) {
    \App::setLocale($locale);
    if ($locale != \Session::get('languageCode')) {
        \Session::set('languageCode', $locale);
    }
}
    else {
    $locale = null;
}

Route::group(
    array('prefix' => $locale),
    function () {

        Route::get('/', array('uses' => 'HomeController@getHome', 'as' => 'home'));

        /**
         * Routes for static pages
         */
        Route::get('our-story', array('uses' => 'PageController@getOurStory', 'as' => 'page:our-story'));
        Route::get('how-it-works', array('uses' => 'PageController@getHowItWorks', 'as' => 'page:how-it-works'));
        Route::get('faq', array('uses' => 'PageController@getFaq', 'as' => 'page:faq'));
        Route::get('team', array('uses' => 'PageController@getTeam', 'as' => 'page:team'));
        Route::get('why-zidisha', array('uses' => 'PageController@getWhyZidisha', 'as' => 'page:why-zidisha'));
        Route::get(
            'trust-and-security',
            array('uses' => 'PageController@getTrustAndSecurity', 'as' => 'page:trust-and-security')
        );
        Route::get('press', array('uses' => 'PageController@getPress', 'as' => 'page:press'));
        Route::get('terms-of-use', array('uses' => 'PageController@getTermsOfUse', 'as' => 'page:terms-of-use'));
        Route::get('contact', array('uses' => 'PageController@getContact', 'as' => 'page:contact'));
        Route::get('volunteer', array('uses' => 'PageController@getVolunteer', 'as' => 'page:volunteer'));
        Route::get('donate', array('uses' => 'PageController@getDonate', 'as' => 'page:donate'));
        Route::get(
            'volunteer-mentor-guidelines',
            array(
                'uses' => 'PageController@getVolunteerMentorGuidelines',
                'as'   => 'page:volunteer-mentor-guidelines'
            )
        );
        Route::get(
            'volunteer-mentor-code-of-ethics',
            array(
                'uses' => 'PageController@getVolunteerMentorCodeOfEthics',
                'as'   => 'page:volunteer-mentor-code-of-ethics'
            )
        );
        Route::get(
            'volunteer-mentor-faq',
            array(
                'uses' => 'PageController@getVolunteerMentorFaq',
                'as'   => 'page:volunteer-mentor-faq'
            )
        );
        Route::get(
            'feature-criteria',
            array(
                'uses' => 'PageController@getFeatureCriteria',
                'as'   => 'page:loan-feature-criteria'
            )
        );
        Route::get(
            'statistics/{timePeriod?}/{country?}',
            array(
                'uses' => 'PageController@getStatistics',
                'as'   => 'page:statistics'
            )
        );
        /**
         * Routes for Authentication
         */
        Route::get('/join', array('uses' => 'AuthController@getJoin', 'as' => 'join'));
        Route::get('lender/join', array('uses' => 'LenderJoinController@getJoin', 'as' => 'lender:join', 'before' => 'loggedIn'));
        Route::post('lender/join', array('uses' => 'LenderJoinController@postJoin','as' => 'lender:post-join', 'before' => 'csrf'));
        Route::post('lender/join-lend', array('uses' => 'LenderJoinController@postJoinLend','as' => 'lender:join-lend', 'before' => 'csrf'));

        Route::get(
            'lender/facebook/join',
            array('uses' => 'LenderJoinController@getFacebookJoin', 'as' => 'lender:facebook-join')
        );
        Route::post(
            'lender/facebook/join',
            array('uses' => 'LenderJoinController@postFacebookJoin', 'as' => 'lender:post-facebook-join', 'before' => 'csrf')
        );
        Route::get(
            'lender/google/join',
            array('uses' => 'LenderJoinController@getGoogleJoin', 'as' => 'lender:google-join')
        );
        Route::post(
            'lender/google/join',
            array('uses' => 'LenderJoinController@postGoogleJoin','as' => 'lender:post-google-join', 'before' => 'csrf')
        );
        Route::post(
            'lender/google/invite',
            array('uses' => 'LenderJoinController@postGoogleInvite','as' => 'lender:post-invite-google', 'before' => 'csrf')
        );

        Route::get('borrower/join', array('uses' => 'BorrowerJoinController@getCountry', 'as' => 'borrower:join', 'before' => 'loggedIn'));
        Route::get(
            'borrower/join/profile/{city}',
            array(
                'uses' => 'BorrowerJoinController@getVolunteerMentorByCity',
                'as'   => 'borrower:join-city'
            )
        );
        Route::post('borrower/join', array('uses' => 'BorrowerJoinController@postCountry', 'before' => 'csrf'));

        Route::controller('borrower/join', 'BorrowerJoinController', ['before' => 'csrf']);


        Route::get('/login', array('uses' => 'AuthController@getLogin', 'as' => 'login', 'before' => 'loggedIn'));
        Route::post('/login', array('uses' => 'AuthController@postLogin', 'before' => ''));
        Route::get('facebook/login', array('uses' => 'AuthController@getFacebookLogin', 'as' => 'facebook:login'));
        Route::get('google/login', array('uses' => 'AuthController@getGoogleLogin', 'as' => 'google:login'));

        Route::get('/logout', array('uses' => 'AuthController@getLogout', 'as' => 'logout'));

        /**
         * Routes for lend page
         */
        Route::get(
            'lend/{category?}/{country?}',
            array('uses' => 'LendController@getIndex', 'as' => 'lend:index')
        );

        /**
         * Routes for Password Reminder page.
         */
        Route::controller('password', 'RemindersController', ['before' => 'csrf']);

        /**
         * Routes for lender page
         */

        Route::get(
            'lender/profile/view/{id}',
            array('uses' => 'LenderController@getPublicProfile', 'as' => 'lender:public-profile')
        );

        Route::group(
            array('prefix' => 'lender', 'before' => 'auth|hasRole:admin:lender'),
            function () {
                Route::get(
                    'history',
                    array('uses' => 'LenderController@getTransactionHistory', 'as' => 'lender:history')
                );

                Route::get(
                    '/loans',
                    array('uses' => 'LenderController@getLoans', 'as' => 'lender:loans')
                );
            }
        );
        
        Route::group(
            array('prefix' => 'lender', 'before' => 'auth|hasRole:lender'),
            function () {

                Route::get(
                    'profile/edit',
                    array('uses' => 'LenderController@getEditProfile', 'as' => 'lender:edit-profile')
                );
                Route::post(
                    'profile/edit',
                    array(
                        'uses'   => 'LenderController@postEditProfile',
                        'as'     => 'lender:post-profile',
                        'before' => 'csrf'
                    )
                );
                Route::get('dashboard', array('uses' => 'LenderController@getDashboard', 'as' => 'lender:dashboard'));
                Route::get('welcome', array('uses' => 'LenderController@getWelcome', 'as' => 'lender:welcome'));
                
                Route::get('funds', array('uses' => 'LenderController@getFunds', 'as' => 'lender:funds'));
                Route::post(
                    'funds',
                    array('uses' => 'LenderController@postFunds', 'as' => 'lender:post-funds', 'before' => 'csrf')
                );

                /**
                 * Routes for loan page
                 */
                Route::post('loan/{loanId}/place-bid', array('uses' => 'LoanController@postPlaceBid', 'as' => 'loan:place-bid', 'before' => 'csrf'));
                Route::post('loan/{loanId}/edit-bid/{bidId}', array('uses' => 'LoanController@postEditBid', 'as' => 'loan:edit-bid', 'before' => 'csrf'));
                
                Route::get(
                    'gift-cards',
                    array('uses' => 'GiftCardController@getGiftCards', 'as' => 'lender:gift-cards')
                );
                Route::post(
                    'gift-cards',
                    array(
                        'uses'   => 'GiftCardController@postGiftCards',
                        'as'     => 'lender:post-gift-cards',
                        'before' => 'csrf'
                    )
                );

                Route::get(
                    '/gift-cards/accept',
                    array(
                        'uses' => 'GiftCardController@getTermsAccept',
                        'as'   => 'lender:gift-cards:terms-accept'
                    )
                );
                Route::post(
                    '/gift-cards/accept',
                    array(
                        'uses' => 'GiftCardController@postTermsAccept',
                        'as'   => 'lender:gift-cards:post-terms-accept'
                    )
                );
                Route::post(
                    'redeem',
                    array(
                        'uses'   => 'GiftCardController@postRedeemCard',
                        'as'     => 'lender:post-redeem-card',
                        'before' => 'csrf'
                    )
                );
                Route::post(
                    'withdraw',
                    array(
                        'uses'   => 'LenderController@postWithdrawalFund',
                        'as'     => 'lender:post-withdraw-funds',
                        'before' => 'csrf'
                    )
                );
                Route::get(
                    '/gift-cards/track',
                    array(
                        'uses' => 'GiftCardController@getTrackCards',
                        'as'   => 'lender:gift-cards:track'
                    )
                );
                Route::get(
                    '/groups/create',
                    array(
                        'uses' => 'LendingGroupController@getCreateGroup',
                        'as'   => 'lender:groups:create'
                    )
                );
                Route::post(
                    '/groups/create',
                    array(
                        'uses' => 'LendingGroupController@postCreateGroup',
                        'as'   => 'lender:groups:post-create'
                    )
                );
                Route::get(
                    'group-create/success/{id}',
                    array(
                        'uses' => 'LendingGroupController@getCreateSuccess',
                        'as' => 'lender:group:create:success'
                    )
                );
                Route::get(
                    'group-join/success/{id}',
                    array(
                        'uses' => 'LendingGroupController@getJoinSuccess',
                        'as' => 'lender:group:join:success'
                    )
                );
                Route::get('groups/{id}/join', array(
                        'uses' => 'LendingGroupController@joinGroup',
                        'as'   => 'lender:group:join'
                    )
                );
                Route::get('groups/{id}/leave', array(
                        'uses' => 'LendingGroupController@leaveGroup',
                        'as'   => 'lender:group:leave'
                    )
                );
                Route::get(
                    '/groups/{id}/edit',
                    array(
                        'uses' => 'LendingGroupController@getEditGroup',
                        'as'   => 'lender:groups:edit'
                    )
                );
                Route::post(
                    '/groups/{id}/edit',
                    array(
                        'uses' => 'LendingGroupController@postEditGroup',
                        'as'   => 'lender:groups:post-edit'
                    )
                );

                Route::get(
                    '/preferences',
                    array(
                        'uses' => 'LenderPreferencesController@getAccountPreference',
                        'as'   => 'lender:preference'
                    )
                );
                Route::post(
                    '/preferences',
                    array(
                        'uses' => 'LenderPreferencesController@postAccountPreference',
                        'as'   => 'lender:post:preference'
                    )
                );

                Route::post(
                    '/follow/{borrowerId}',
                    array(
                        'uses' => 'FollowController@postFollow',
                        'as'   => 'lender:follow'
                    )
                );
                Route::post(
                    '/unfollow/{borrowerId}',
                    array(
                        'uses' => 'FollowController@postUnfollow',
                        'as'   => 'lender:unfollow'
                    )
                );
                Route::post(
                    '/update-follower/{borrowerId}',
                    array(
                        'uses' => 'FollowController@postUpdateFollower',
                        'as'   => 'lender:update-follower'
                    )
                );
                
                Route::get('/auto-lending', array('uses' => 'LenderPreferencesController@getAutoLending', 'as' => 'lender:auto-lending'));
                Route::post('/auto-lending/{lenderId}', array('uses' => 'LenderPreferencesController@postAutoLending', 'as' => 'lender:post:auto-lending'));


                Route::get('lender-accept-forgive-loan/{verificationCode}', [ 'uses' => 'AdminLoanForgivenessController@lenderAcceptLoanForgiveness', 'as'   => 'lender:loan-forgiveness:accept']);
                Route::get('lender-reject-forgive-loan/{verificationCode}', [ 'uses' => 'AdminLoanForgivenessController@lenderRejectLoanForgiveness', 'as'   => 'lender:loan-forgiveness:reject']);

                Route::get(
                    '/following',
                    array(
                        'uses' => 'FollowController@getFollowing',
                        'as'   => 'lender:following'
                    )
                );
            }
        );

        /**
         * Routes for borrower page
         */
        Route::get(
            'borrower/profile/view/{username}',
            array('uses' => 'BorrowerController@getPublicProfile', 'as' => 'borrower:public-profile')
        );

        Route::group(
            array('prefix' => 'borrower', 'before' => 'auth|hasRole:borrower'),
            function () {
                Route::get(
                    'personal-information',
                    array(
                        'uses' => 'BorrowerController@getPersonalInformation',
                        'as'   => 'borrower:personal-information'
                    )
                );

                Route::post(
                    'personal-information',
                    array(
                        'uses' => 'BorrowerController@postPersonalInformation',
                        'as'   => 'borrower:post-personal-information'
                    )
                );

                Route::get(
                    'facebook/verification',
                    array('uses' => 'BorrowerController@getFacebookRedirect', 'as' => 'borrower:facebook-verification')
                );

                Route::get(
                    'profile/edit',
                    array('uses' => 'BorrowerController@getEditProfile', 'as' => 'borrower:edit-profile')
                );
                Route::post(
                    'profile/edit',
                    array(
                        'uses'   => 'BorrowerController@postEditProfile',
                        'as'     => 'borrower:post-profile',
                        'before' => 'csrf'
                    )
                );
                Route::post(
                    'delete/upload',
                    array(
                        'uses'   => 'BorrowerController@postDeleteUpload',
                        'as'     => 'borrower:delete-upload',
                        'before' => 'csrf'
                    )
                );
                Route::get(
                    'dashboard',
                    array('uses' => 'BorrowerController@getDashboard', 'as' => 'borrower:dashboard')
                );
                Route::get(
                    'loan-application',
                    array('uses' => 'LoanApplicationController@getInstructions', 'as' => 'borrower:loan-application')
                );
                Route::post('loan-application', array('uses' => 'LoanApplicationController@postInstructions'));
                Route::controller('loan-application', 'LoanApplicationController');
                Route::get(
                    'history',
                    array('uses' => 'BorrowerController@getTransactionHistory', 'as' => 'borrower:history')
                );
                Route::get(
                    'resend-verification-mail',
                    array('uses' => 'BorrowerController@resendVerificationMail', 'as' => 'borrower:resend:verification')
                );

                Route::get('loan/{loanId?}', [ 'uses' => 'BorrowerLoanController@getLoan', 'as' => 'borrower:loan' ] );
                Route::post('loan/{loanId}/accept-bids', 'BorrowerLoanController@postAcceptBids');

                Route::get('invite', array('uses' => 'BorrowerInviteController@getInvite', 'as' => 'borrower:invite'));
                Route::post(
                    'invite',
                    array('uses' => 'BorrowerInviteController@postInvite', 'as' => 'borrower:post-invite', 'before' => 'csrf')
                );
                Route::get('invites', array('uses' => 'BorrowerInviteController@getInvites', 'as' => 'borrower:invites'));
                Route::post(
                    'delete-invite/{id}',
                    array('uses' => 'BorrowerInviteController@postDeleteInvite', 'as' => 'borrower:delete-invite', 'before' => 'csrf')
                );
                Route::get('current-credit', [ 'uses' => 'BorrowerController@getCurrentCredit', 'as' => 'borrower:credit']);

                Route::get(
                    'reschedule-loan',
                    array('uses' => 'BorrowerLoanController@getRescheduleLoan', 'as' => 'borrower:reschedule-loan')
                );
                Route::post(
                    'reschedule-loan',
                    array('uses' => 'BorrowerLoanController@postRescheduleLoan', 'as' => 'borrower:post-reschedule-loan')
                );
                Route::get(
                    'reschedule-loan-confirmation',
                    array('uses' => 'BorrowerLoanController@getRescheduleLoanConfirmation', 'as' => 'borrower:reschedule-loan-confirmation')
                );
                Route::post(
                    'reschedule-loan-confirmation',
                    array('uses' => 'BorrowerLoanController@postRescheduleLoanConfirmation', 'as' => 'borrower:post-reschedule-loan-confirmation')
                );
            }
        );

        Route::get(
            'borrower/verification/{verificationCode}',
            array('uses' => 'AuthController@verifyBorrower', 'as' => 'borrower:verify')
        );
        Route::get(
            'borrower/resume/{resumeCode}',
            array('uses' => 'AuthController@resumeApplication', 'as' => 'borrower:resumeApplication')
        );
        Route::post(
            'borrower/resume',
            array('uses' => 'AuthController@postResumeApplication', 'as' => 'borrower:post:resumeApplication')
        );

        /**
         * Routes for Volunteer Mentor
         */
        Route::group(
            array('prefix' => 'volunteer-mentor', 'before' => 'auth|hasSubRole:volunteerMentor'),
            function () {
                Route::get(
                    'assigned-members',
                    ['uses' => 'VolunteerMentorController@getAssignedMembers', 'as' => 'volunteer-mentor:get:assigned-members']
                );
            }
        );

        /**
         * Routes for loan page
         */
        Route::get('loan/{loanId}', array('uses' => 'LoanController@getIndex', 'as' => 'loan:index'));
        Route::get('loan-success/{loanId}', array('uses' => 'LoanController@getLoanSuccess', 'as' => 'loan:success'));

        /**
         * Routes for BorrowerComments
         */
        Route::controller('borrowercomment/{id}', 'BorrowerCommentController');

        /**
         * Routes for LoanFeedbackComments
         */
        Route::controller('loanfeedback/{id}', 'LoanFeedbackController');


        /**
         * Routes for LendingGroupComments
         */
        Route::controller('lendinggroupcomment/{id}', 'LendingGroupCommentController');

        /**
         * Routes for Admin
         */
        Route::group(
            array('prefix' => 'admin', 'before' => 'auth|hasRole:admin'),
            function () {
                Route::get(
                    'test-mails',
                    ['uses' => 'MailTesterController@getAllMails', 'as' => 'admin:mail:test-mails']
                );

                Route::post(
                    'test-mails',
                    ['uses' => 'MailTesterController@postMail', 'as' => 'admin:mail:post:mail']
                );

                Route::post(
                    'withdrawal-requests/paypal/pay',
                    array('uses' => 'AdminController@postPaypalWithdrawalRequests',
                          'as' => 'admin:post:paypal-withdrawal-requests')

                );
                Route::post(
                    'withdrawal-requests/{withdrawalRequestId}/pay',
                    array('uses' => 'AdminController@postWithdrawalRequests', 'as' => 'admin:post:withdrawal-requests')

                );
                Route::get(
                    'countries/edit/{id}',
                    array('uses' => 'CountryController@editCountry', 'as' => 'admin:edit:country')
                );
                Route::post(
                    'countries/edit/{id}',
                    array('uses' => 'CountryController@postEditCountry', 'as' => 'admin:post:edit:country')
                );
                Route::post(
                    '/settings/exchange-rates/{countryName?}',
                    array(
                        'uses'   => 'AdminController@postExchangeRates',
                        'as'     => 'admin:post-exchange-rates',
                        'before' => 'csrf'
                    )
                );
            }
        );

        /**
         * Routes for Admin|VolunteerMentor
         */
        Route::group(
            array('prefix' => 'admin', 'before' => 'auth|isVolunteerMentorOrAdmin'),
            function () {
                Route::get(
                    'personal-information/{username?}',
                    array(
                        'uses' => 'BorrowerController@getPersonalInformation',
                        'as'   => 'admin:borrower:personal-information'
                    )
                );

                Route::get(
                    'vm/repayments/schedule/{borrowerId}/{loanId?}',
                    array(
                        'uses' => 'AdminController@getRepaymentSchedule',
                        'as'   => 'admin:vm:repayment-schedule'
                    )
                );
            }
        );

        /**
         * Routes for Admin|Volunteer
         */
        Route::group(
            array('prefix' => 'admin', 'before' => 'auth|isVolunteerOrAdmin'),
            function () {
                Route::get('dashboard', array('uses' => 'AdminController@getDashboard', 'as' => 'admin:dashboard'));
                Route::get('volunteers', array('uses' => 'AdminController@getVolunteers', 'as' => 'admin:volunteers'));
                Route::get(
                    'volunteers/add/{id}',
                    array('uses' => 'AdminController@addVolunteer', 'as' => 'admin:add:volunteer')
                );
                Route::get(
                    'volunteers/remove/{id}',
                    array('uses' => 'AdminController@removeVolunteer', 'as' => 'admin:remove:volunteer')
                );
                Route::get('volunteer-mentors', array('uses' => 'AdminController@getVolunteerMentors', 'as' => 'admin:volunteer-mentors'));
                Route::get('add-volunteer-mentors', array('uses' => 'AdminController@getAddVolunteerMentors', 'as' => 'admin:add:volunteer-mentors'));
                Route::get(
                    'volunteer-mentors/add/{id}',
                    array('uses' => 'AdminController@addVolunteerMentor', 'as' => 'admin:add:volunteer-mentor')
                );
                Route::get(
                    'volunteer-mentors/remove/{id}',
                    array('uses' => 'AdminController@removeVolunteerMentor', 'as' => 'admin:remove:volunteer-mentor')
                );
                Route::get('borrowers', array('uses' => 'AdminController@getBorrowers', 'as' => 'admin:borrowers'));
                Route::get(
                    'borrowers/{borrowerId}',
                    array('uses' => 'AdminController@getBorrower', 'as' => 'admin:borrower')
                );
                Route::get(
                    'borrowers/edit/{borrowerId}',
                    array('uses' => 'AdminController@getBorrowerEdit', 'as' => 'admin:borrower:edit')
                );
                Route::post(
                    'borrowers/edit/{borrowerId}',
                    array('uses' => 'AdminController@postBorrowerEdit', 'as' => 'admin:borrower:edit:post', 'before' => 'csrf')
                );
                Route::get('lenders', array('uses' => 'AdminController@getLenders', 'as' => 'admin:lenders'));
                
                Route::post('lender/delete/{lenderId}', array('uses' => 'AdminController@postDeleteLender', 'as' => 'admin:delete:lender'));
                Route::post('lender/deactivate/{lenderId}', array('uses' => 'AdminController@postDeactivateLender', 'as' => 'admin:deactivate:lender'));
                Route::post('lender/activate/{lenderId}', array('uses' => 'AdminController@postActivateLender', 'as' => 'admin:activate:lender'));
                
                Route::post('lender/last-check-in-email/{lenderId}', array('uses' => 'AdminController@postLastCheckInEmail', 'as' => 'admin:last-check-in-email:lender'));
                
                Route::get('loans', array('uses' => 'AdminController@getLoans', 'as' => 'admin:loans'));
                Route::get(
                    '/settings/exchange-rates/{countryName?}',
                    array(
                        'uses' => 'AdminController@getExchangeRates',
                        'as'   => 'admin:exchange-rates'
                    )
                );

                Route::get(
                    '/repayments',
                    array(
                        'uses' => 'AdminController@getRepayments',
                        'as'   => 'admin:repayments'
                    )
                );
                Route::post(
                    '/upload-repayments',
                    array(
                        'uses'   => 'AdminController@postUploadRepayments',
                        'as'     => 'admin:upload-repayments',
                        'before' => 'csrf'
                    )
                );
                Route::get(
                    '/repayments/process/{name?}',
                    array(
                        'uses' => 'AdminController@getRepaymentProcess',
                        'as'   => 'admin:repayment-process'
                    )
                );
                Route::post(
                    '/repayments/process/{name?}',
                    array(
                        'uses'   => 'AdminController@postRepaymentProcess',
                        'as'     => 'admin:post-repayment-process',
                        'before' => 'csrf'
                    )
                );
                Route::get(
                    '/repayments/refunds',
                    array(
                        'uses' => 'AdminController@getRepaymentRefund',
                        'as'   => 'admin:repayments-refunds'
                    )
                );
                Route::post(
                    '/repayments/refunds',
                    array(
                        'uses'   => 'AdminController@postRepaymentRefund',
                        'as'     => 'admin:post-repayments-refunds',
                        'before' => 'csrf'
                    )
                );
                Route::get(
                    '/repayments/schedule/{borrowerId}/{loanId?}',
                    array(
                        'uses' => 'AdminController@getRepaymentSchedule',
                        'as'   => 'admin:repayment-schedule'
                    )
                );
                Route::post(
                    '/repayments/enter-repayment/{loanId?}',
                    array(
                        'uses' => 'AdminController@postEnterRepayment',
                        'as'   => 'admin:enter-repayment'
                    )
                );
                Route::get(
                    'borrower-activation',
                    array('uses' => 'BorrowerActivationController@getIndex', 'as' => 'admin:borrower-activation')
                );
                Route::get(
                    'borrower-activation/{borrowerId}',
                    array(
                        'uses' => 'BorrowerActivationController@getEdit',
                        'as'   => 'admin:borrower-activation:edit'
                    )
                );
                Route::post(
                    'borrower-activation/{borrowerId}/review',
                    array(
                        'uses' => 'BorrowerActivationController@postReview',
                        'as'   => 'admin:borrower-activation:review'
                    )
                );
                Route::post(
                    'borrower-activation/{borrowerId}/feedback',
                    array(
                        'uses' => 'BorrowerActivationController@postFeedback',
                        'as'   => 'admin:borrower-activation:feedback'
                    )
                );
                Route::post(
                    'borrower-activation/{borrowerId}/verification',
                    array(
                        'uses' => 'BorrowerActivationController@postVerification',
                        'as'   => 'admin:borrower-activation:verification'
                    )
                );
                Route::get(
                    'loan-feedback/{loanId}',
                    array(
                        'uses' => 'AdminController@getLoanFeedback',
                        'as'   => 'admin:loan-feedback'
                    )
                );
                Route::post(
                    'loan-feedback',
                    array(
                        'uses' => 'AdminController@postLoanFeedback',
                        'as'   => 'admin:post-loan-feedback'
                    )
                );
                Route::post(
                    'loan/{id}',
                    array(
                        'uses'   => 'AdminController@postAdminCategory',
                        'as'     => 'admin:post-category',
                        'before' => 'csrf'
                    )
                );
                Route::get(
                    'loan/{id}/translate',
                    array('uses' => 'AdminController@getTranslate', 'as' => 'admin:get-translate')
                );
                Route::post(
                    'loan/{id}/translate',
                    array(
                        'uses'   => 'AdminController@postTranslate',
                        'as'     => 'admin:post-translate',
                        'before' => 'csrf'
                    )
                );
                Route::post(
                    'loan/{id}',
                    array('uses' => 'AdminController@postAdminCategory', 'as' => 'admin:post-category')
                );
                Route::get('countries', array('uses' => 'CountryController@getCountries', 'as' => 'admin:countries'));

                Route::get('settings', array('uses' => 'AdminController@getSettings', 'as' => 'admin:settings'));
                Route::post('settings', array('uses' => 'AdminController@postSettings', 'as' => 'admin:settings'));

                Route::get(
                    '/translation-feed/{type?}',
                    array(
                        'uses' => 'AdminController@getTranslationFeed',
                        'as'   => 'admin:get:translation-feed'
                    )
                );

                Route::get(
                    'gift-cards',
                    array('uses' => 'AdminController@getGiftCards', 'as' => 'admin:get:gift-cards')
                );
                Route::get(
                    'gift-cards/resend/{id}',
                    array('uses' => 'AdminController@resendEmailToRecipient', 'as' => 'admin:resend')
                );

                Route::get(
                    'translation',
                    ['uses' => 'TranslationController@getTranslation', 'as' => 'admin:translation:index']
                );

                Route::get(
                    'translation/{folder}/{filename}/{languageCode}',
                    ['uses' => 'TranslationController@getTranslations', 'as' => 'admin:translation']
                );

                Route::post(
                    'translation/{folder}/{filename}/{languageCode}',
                    ['uses' => 'TranslationController@postTranslations', 'as' => 'admin:translation:post']
                );
                
                Route::post(
                    'pending-disbursements',
                    ['uses' => 'PendingDisbursementsController@postPendingDisbursements', 'as' => 'admin:pending-disbursements']
                );

                Route::get(
                    'pending-disbursements/{countryId?}',
                    ['uses' => 'PendingDisbursementsController@getPendingDisbursements', 'as' => 'admin:pending-disbursements']
                );

                Route::post(
                    'pending-disbursements/loanNote',
                    ['uses' => 'PendingDisbursementsController@postLoanNote']
                );

                Route::post(
                    'volunteer-mentors/VmNote',
                    ['uses' => 'AdminController@postVmNote']
                );

                Route::post(
                    'pending-disbursements/authorize/{loanId}',
                    ['uses' => 'PendingDisbursementsController@postAuthorize']
                );

                Route::post(
                    'pending-disbursements/disburse/{loanId}',
                    ['uses' => 'PendingDisbursementsController@postDisburse']
                );

                Route::get(
                    'withdrawal-requests',
                    array('uses' => 'AdminController@getWithdrawalRequests', 'as' => 'admin:get:withdrawal-requests')
                );

                Route::get(
                    'publish-comments',
                    array('uses' => 'AdminController@getPublishComments', 'as' => 'admin:moderate-comments')
                );

                Route::post(
                    'publish-comments',
                    array('uses' => 'AdminController@postPublishComments', 'as' => 'admin:post:moderate-comments')
                );

                Route::get(
                    'test-sms',
                    ['uses' => 'SmsTesterController@getAllSms', 'as' => 'admin:sms:test-sms']
                );

                Route::post(
                    'test-sms',
                    ['uses' => 'SmsTesterController@postSms', 'as' => 'admin:sms:post:sms']
                );

                Route::get(
                    'test-sift-science',
                    ['uses' => 'SiftScienceTesterController@getAllSiftScienceEvents', 'as' => 'admin:test:sift-science']
                );

                Route::post(
                    'test-sift-science',
                    ['uses' => 'SiftScienceTesterController@postSiftScienceEvent', 'as' => 'admin:post:test:sift-science']
                );
                
                Route::get(
                    'loan-forgiveness/{countryCode?}',
                    ['uses' => 'AdminLoanForgivenessController@getIndex', 'as' => 'admin:loan-forgiveness:index']
                );

                Route::get(
                    'allow-loan-forgiveness/{countryCode}',
                    ['uses' => 'AdminLoanForgivenessController@getAllow', 'as' => 'admin:loan-forgiveness:allow']
                );

                Route::post(
                    'allow-loan-forgiveness',
                    ['uses' => 'AdminLoanForgivenessController@postAllow']
                );

                Route::get(
                    'allow-loan-forgiveness-loans',
                    ['uses' => 'AdminLoanForgivenessController@getLoans', 'as' => 'admin:loan-forgiveness:loans']
                );
            }
        );

        /**
         * PayPal Controller
         */
        Route::controller('paypal', 'PayPalController');

        /**
         * Image resize route
         */
        Route::get('resize/{uploadId}/{format}', array('uses' => 'ImageController@getImage', 'as' => 'image:resize'));

        /**
         * Routes for Invite
         */
        Route::get('invite', array('uses' => 'LenderInviteController@getInvite', 'as' => 'lender:invite'));
        Route::get('i/{id}', array('uses' => 'LenderInviteController@getInvitee', 'as' => 'lender:invitee'));
        Route::get(
            'invite/how-it-works',
            array('uses' => 'LenderInviteController@getHowItWorks', 'as' => 'lender:how-it-works')
        );
        Route::post(
            'invite',
            array('uses' => 'LenderInviteController@postInvite', 'as' => 'lender:post-invite', 'before' => 'csrf')
        );
        Route::get('groups', array(
            'uses' => 'LendingGroupController@getGroups',
            'as'   => 'lender:groups'
            )
        );
        Route::get('groups/{id}', array(
                'uses' => 'LendingGroupController@getGroup',
                'as'   => 'lender:group'
            )
        );

        /**
         * Route for project updates
         */
        Route::get('project-updates', array(
                'uses' => 'PageController@getProjectUpdates',
                'as'   => 'project-updates'
            )
        );
    }
);
