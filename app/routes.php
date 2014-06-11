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
Route::post('/join', array('uses' => 'AuthController@postJoin', 'before' => 'csrf'));

Route::get('/login', array('uses' => 'AuthController@getLogin', 'as' => 'login'));
Route::post('/login', array('uses' => 'AuthController@postLogin', 'before' => 'csrf'));

Route::get('/logout', array('uses' => 'AuthController@getLogout', 'as' => 'logout'));

Route::get('auth/facebook/join', array('uses' => 'AuthController@getFacebookJoin', 'as' => 'facebook:join'));
Route::get('auth/facebook/login', array('uses' => 'AuthController@getFacebookLogin', 'as' => 'facebook:login'));
Route::post('auth/facebook/confirm', array('uses' => 'AuthController@postFacebookConfirm', 'as' => 'facebook:confirm'));

/**
 * Routes for lend page
 */
Route::get('lend', array('uses' => 'LendController@getIndex', 'as' => 'lend:index'));

/**
 * Routes for Password Reminder page.
 */
Route::controller('password', 'RemindersController', ['before' => 'csrf']);

/**
 * Routes for lender page
 */
Route::get('lender/profile', array('uses' => 'LenderController@getPublicProfile', 'as' => 'lender:public-profile', 'before' => 'auth|hasRole:lender'));
Route::get('lender/profile/edit', array('uses' => 'LenderController@getEditProfile', 'as' => 'lender:edit-profile'));
Route::post('lender/profile/edit', array('uses' => 'LenderController@postEditProfile', 'as' => 'lender:post-profile', 'before' => 'csrf'));

/**
 * Routes for borrower page
 */
Route::get('borrower/profile', array('uses' => 'BorrowerController@getPublicProfile', 'as' => 'borrower:public-profile'));
Route::get('borrower/profile/edit', array('uses' => 'BorrowerController@getEditProfile', 'as' => 'borrower:edit-profile'));
Route::post('borrower/profile/edit', array('uses' => 'BorrowerController@postEditProfile', 'as' => 'borrower:post-profile', 'before' => 'csrf'));
