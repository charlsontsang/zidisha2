<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('uses' => 'HomeController@getHome', 'as' => 'home'));

/**
 * Routes for static pages
 */
Route::get('our-story', array('uses' => 'PageController@getOurStory', 'as' => 'page:our-story'));
Route::get('how-it-works', array('uses' => 'PageController@getHowItWorks', 'as' => 'page:how-it-works'));
Route::get('why-zidisha', array('uses' => 'PageController@getWhyZidisha', 'as' => 'page:why-zidisha'));
Route::get(
    'trust-and-security',
    array('uses' => 'PageController@getTrustAndSecurity', 'as' => 'page:trust-and-security')
);
Route::get('press', array('uses' => 'PageController@getPress', 'as' => 'page:press'));
Route::get('terms-of-use', array('uses' => 'PageController@getTermsOfUse', 'as' => 'page:terms-of-use'));
Route::get('volunteer-mentor-guidelines', array('uses' => 'PageController@getVolunteerMentorGuidelines',
        'as' => 'page:volunteer-mentor-guidelines'));
Route::get('volunteer-mentor-code-of-ethics', array('uses' => 'PageController@getVolunteerMentorCodeOfEthics',
        'as' => 'page:volunteer-mentor-code-of-ethics'));
Route::get('volunteer-mentor-faq', array('uses' => 'PageController@getVolunteerMentorFaq',
        'as' => 'page:volunteer-mentor-faq'));
Route::get('feature-criteria', array('uses' => 'PageController@getFeatureCriteria',
        'as' => 'page:loan-feature-criteria'));

/**
 * Routes for Authentication
 */
Route::get('/join', array('uses' => 'AuthController@getJoin', 'as' => 'join'));
Route::get('lender/join', array('uses' => 'LenderJoinController@getJoin', 'as' => 'lender:join'));
Route::post('lender/join', array('uses' => 'LenderJoinController@postJoin', 'before' => 'csrf'));

Route::get('lender/facebook/join', array('uses' => 'LenderJoinController@getFacebookJoin', 'as' => 'lender:facebook-join'));
Route::post('lender/facebook/join', array('uses' => 'LenderJoinController@postFacebookJoin', 'before' => 'csrf'));

Route::get('borrower/join', array('uses' => 'BorrowerJoinController@getCountry', 'as' => 'borrower:join'));
Route::get(
    'borrower/join/profile/{city}',
    array(
        'uses' => 'BorrowerJoinController@getVolunteerMentorByCity',
        'as' => 'borrower:join-city'
    )
);
Route::post('borrower/join', array('uses' => 'BorrowerJoinController@postCountry', 'before' => 'csrf'));

Route::controller('borrower/join', 'BorrowerJoinController', ['before' => 'csrf']);

Route::get('/login', array('uses' => 'AuthController@getLogin', 'as' => 'login'));
Route::post('/login', array('uses' => 'AuthController@postLogin', 'before' => 'csrf'));
Route::get('facebook/login', array('uses' => 'AuthController@getFacebookLogin', 'as' => 'facebook:login'));


Route::get('/logout', array('uses' => 'AuthController@getLogout', 'as' => 'logout'));

/**
 * Routes for lend page
 */
Route::get('lend/{stage?}/{category?}/{country?}', array('uses' => 'LendController@getIndex', 'as' => 'lend:index'));

/**
 * Routes for borrow page
 */
Route::get('borrow', array('uses' => 'BorrowController@getPage', 'as' => 'borrow.page'));

/**
 * Routes for Password Reminder page.
 */
Route::controller('password', 'RemindersController', ['before' => 'csrf']);

/**
 * Routes for lender page
 */

Route::get(
    'lender/profile/view/{username}',
    array('uses' => 'LenderController@getPublicProfile', 'as' => 'lender:public-profile')
);

Route::group(
    array('prefix' => 'lender', 'before' => 'auth|hasRole:lender'),
    function () {

        Route::get('profile/edit', array('uses' => 'LenderController@getEditProfile', 'as' => 'lender:edit-profile'));
        Route::post(
            'profile/edit',
            array('uses' => 'LenderController@postEditProfile', 'as' => 'lender:post-profile', 'before' => 'csrf')
        );
        Route::get('dashboard', array('uses' => 'LenderController@getDashboard', 'as' => 'lender:dashboard'));
        Route::get('history', array('uses' => 'LenderController@getTransactionHistory', 'as' => 'lender:history'));
        Route::get('funds', array('uses' => 'LenderController@getFunds', 'as' => 'lender:funds'));
        Route::post('funds', array('uses' => 'LenderController@postFunds', 'as' => 'lender:post-funds', 'before' => 'csrf'));
        Route::get('gift-cards', array('uses' => 'GiftCardController@getGiftCards', 'as' => 'lender:gift-cards'));
        Route::post(
            'gift-cards',
            array('uses' => 'GiftCardController@postGiftCards', 'as' => 'lender:post-gift-cards', 'before' => 'csrf')
        );


        Route::post(
            '/gift-cards/accept',
            array(
                'uses' => 'GiftCardController@postTermsAccept',
                'as' => 'lender:gift-cards:terms-accept'
            )
        );
        Route::post(
            'redeem',
            array(
                'uses' => 'GiftCardController@postRedeemCard',
                'as' => 'lender:post-redeem-card',
                'before' => 'csrf'
            )
        );
        Route::get(
            '/gift-cards/track',
            array(
                'uses' => 'GiftCardController@getTrackCards',
                'as' => 'lender:gift-cards:track'
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

        Route::get('personal-information', array('uses' => 'BorrowerController@getPersonalInformation', 'as' => 'borrower:personal-information'));
        Route::post('personal-information', array('uses' => 'BorrowerController@postPersonalInformation', 'as' => 'borrower:post-personal-information'));

        Route::get('facebook/verification', array('uses' => 'BorrowerController@getFacebookRedirect', 'as' => 'borrower:facebook-verification'));

        Route::get('profile/edit', array('uses' => 'BorrowerController@getEditProfile', 'as' => 'borrower:edit-profile'));
        Route::post(
            'profile/edit',
            array('uses' => 'BorrowerController@postEditProfile', 'as' => 'borrower:post-profile', 'before' => 'csrf')
        );
        Route::post(
            'delete/upload',
            array('uses' => 'BorrowerController@postDeleteUpload', 'as' => 'borrower:delete-upload', 'before' => 'csrf')
        );
        Route::get('dashboard', array('uses' => 'BorrowerController@getDashboard', 'as' => 'borrower:dashboard'));
        Route::get('loan-application', array('uses' => 'LoanApplicationController@getInstructions', 'as' => 'borrower:loan-application'));
        Route::post('loan-application', array('uses' => 'LoanApplicationController@postInstructions'));
        Route::controller('loan-application', 'LoanApplicationController');
        Route::get('history', array('uses' => 'BorrowerController@getTransactionHistory', 'as' => 'borrower:history'));
        Route::get('resend-verification-mail', array('uses' => 'BorrowerController@resendVerificationMail', 'as' => 'borrower:resend:verification'));

    }
);

Route::get('borrower/verification/{verificationCode}', array('uses' => 'AuthController@verifyBorrower', 'as' => 'borrower:verify'));
Route::get('borrower/resume/{resumeCode}', array('uses' => 'AuthController@resumeApplication', 'as' => 'borrower:resumeApplication'));
Route::post('borrower/resume', array('uses' => 'AuthController@postResumeApplication', 'as' => 'borrower:post:resumeApplication'));

/**
 * Routes for loan page
 */
Route::get('loan/{id}', array('uses' => 'LoanController@getIndex', 'as' => 'loan:index'));
Route::post('cart', array('uses' => 'LoanController@postBid', 'as' => 'loan:post-bid', 'before' => 'csrf'));

/**
 * Routes for Comments controller
 */
Route::post('comment', array('uses' => 'CommentsController@postComment', 'as' => 'comment:post', 'before' => 'csrf'));
Route::post('edit', array('uses' => 'CommentsController@postEdit', 'as' => 'comment:edit', 'before' => 'csrf'));
Route::post('reply', array('uses' => 'CommentsController@postReply', 'as' => 'comment:reply', 'before' => 'csrf'));
Route::post('translate', array('uses' => 'CommentsController@postTranslate', 'as' => 'comment:translate', 'before' => 'csrf'));
Route::post('delete', array('uses' => 'CommentsController@postDelete', 'as' => 'comment:delete', 'before' => 'csrf'));
Route::post(
    'delete/upload',
    array('uses' => 'CommentsController@postDeleteUpload', 'as' => 'comment:delete-upload', 'before' => 'csrf')
);

/**
 * Routes for Admin
 */
Route::group(
    array('prefix' => 'admin', 'before' => 'auth|hasRole:admin'),
    function () {
        Route::get('dashboard', array('uses' => 'AdminController@getDashboard', 'as' => 'admin:dashboard'));
        Route::get('borrowers', array('uses' => 'AdminController@getBorrowers', 'as' => 'admin:borrowers'));
        Route::get('lenders', array('uses' => 'AdminController@getLenders', 'as' => 'admin:lenders'));
        Route::get('loans', array('uses' => 'AdminController@getLoans', 'as' => 'admin:loans'));
        Route::get('/settings/exchange-rates/{countryName?}', array('uses' => 'AdminController@getExchangeRates' ,
                'as' => 'admin:exchange-rates'));
        Route::post('/settings/exchange-rates/{countryName?}', array('uses' => 'AdminController@postExchangeRates',
                'as' => 'admin:post-exchange-rates', 'before' => 'csrf'));
        Route::get('borrower-activation', array('uses' => 'BorrowerActivationController@getIndex', 'as' => 'admin:borrower-activation'));
        Route::get('borrower-activation/{borrowerId}', array('uses' => 'BorrowerActivationController@getEdit',
                'as' => 'admin:borrower-activation:edit'));
        Route::post('borrower-activation/{borrowerId}/review', array('uses' => 'BorrowerActivationController@postReview', 'as' => 'admin:borrower-activation:review'));
        Route::post('borrower-activation/{borrowerId}/feedback', array('uses' => 'BorrowerActivationController@postFeedback', 'as' => 'admin:borrower-activation:feedback'));
        Route::get('loan-feedback/{loanId}', array('uses' => 'AdminController@getLoanFeedback',
                'as' => 'admin:loan-feedback'));
        Route::post('loan-feedback', array('uses' => 'AdminController@postLoanFeedback',
                'as' => 'admin:post-loan-feedback'));
    }
);
/**
 * Image resize route
 */
Route::get('resize/{uploadId}/{format}', array('uses' => 'ImageController@getImage', 'as' => 'image:resize'));

/**
 * Routes for Invite
 */
Route::get('invite', array('uses' => 'LenderInviteController@getInvite', 'as' => 'lender:invite'));
Route::get('i/{username}', array('uses' => 'LenderInviteController@getInvitee', 'as' => 'lender:invitee'));
Route::get('invite/how-it-works', array('uses' => 'LenderInviteController@getHowItWorks', 'as' => 'lender:how-it-works'));
Route::post('invite', array('uses' => 'LenderInviteController@postInvite', 'as' => 'lender:post-invite', 'before' => 'csrf'));

/**
 * Routes for PayPal Payments Processing
 */
Route::controller('paypal', 'PayPalController');
