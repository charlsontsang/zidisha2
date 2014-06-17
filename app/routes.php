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

/**
 * Routes for Authentication
 */
Route::get('/join', array('uses' => 'AuthController@getJoin', 'as' => 'join'));
Route::get('lender/join', array('uses' => 'LenderJoinController@getJoin', 'as' => 'lender:join'));
Route::post('lender/join', array('uses' => 'LenderJoinController@postJoin', 'before' => 'csrf'));

Route::get('lender/facebook/join', array('uses' => 'LenderJoinController@getFacebookJoin', 'as' => 'lender:facebook-join'));
Route::post('lender/facebook/join', array('uses' => 'LenderJoinController@postFacebookJoin'));

Route::get('borrower/join', array('uses' => 'BorrowerJoinController@getJoin', 'as' => 'borrower:join'));

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

Route::get('lender/profile/view/{username}', array('uses' => 'LenderController@getPublicProfile', 'as' => 'lender:public-profile'));

Route::group(array('prefix' => 'lender', 'before' => 'auth|hasRole:lender'), function()
    {

        Route::get('profile/edit', array('uses' => 'LenderController@getEditProfile', 'as' => 'lender:edit-profile'));
        Route::post('profile/edit', array('uses' => 'LenderController@postEditProfile', 'as' => 'lender:post-profile', 'before' => 'csrf'));
        Route::get('dashboard', array('uses' => 'LenderController@getDashboard', 'as' => 'lender:dashboard'));
        Route::get('history', array('uses' => 'LenderController@getTransactionHistory', 'as' => 'lender:history'));
        Route::get('funds', array('uses' => 'LenderController@getFunds', 'as' => 'lender:funds'));
        Route::post('funds', array('uses' => 'LenderController@postFunds', 'as' => 'lender:post-funds', 'before' => 'csrf'));
    });
    
/**
 * Routes for borrower page
 */
Route::get('borrower/profile/view/{username}', array('uses' => 'BorrowerController@getPublicProfile', 'as' => 'borrower:public-profile'));

Route::group(array('prefix' => 'borrower', 'before' => 'auth|hasRole:borrower'), function()
    {

        Route::get('profile/edit', array('uses' => 'BorrowerController@getEditProfile', 'as' => 'borrower:edit-profile'));
        Route::post('profile/edit', array('uses' => 'BorrowerController@postEditProfile', 'as' => 'borrower:post-profile', 'before' => 'csrf'));
        Route::get('dashboard', array('uses' => 'BorrowerController@getDashboard', 'as' => 'borrower:dashboard'));
        Route::controller('loan-application', 'LoanApplicationController');
        Route::get('history', array('uses' => 'BorrowerController@getTransactionHistory', 'as' => 'borrower:history'));
    });

/**
 * Routes for loan page
 */
Route::get('loan/{id}', array('uses' => 'LoanController@getIndex', 'as' => 'loan:index'));