<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

use Zidisha\Country\CountryQuery;
use Zidisha\User\User;
use Zidisha\Utility\Utility;

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter(
    'hasRole',
    function ($route, $request, $role) {
        $roles = explode(':', $role);
        $isAllowed = in_array(Auth::getUser()->getRole(), $roles);
        if (!$isAllowed) {
            Flash::error("You do not have proper permission to view this page");
            return Redirect::route('login');
        }
    }
);

Route::filter(
    'hasSubRole',
    function ($route, $request, $role) {
        $isAllowed = Auth::getUser()->getSubRole() == $role;
        if (!$isAllowed) {
            Flash::error("You do not have proper permission to view this page");
            return Redirect::route('login');
        }
    }
);

Route::filter(
    'isVolunteerOrAdmin',
    function ($route, $request) {
        $isAllowed = Auth::getUser()->isVolunteerOrAdmin();
        if (!$isAllowed) {
            Flash::error("You do not have proper permission to view this page");
            return Redirect::route('login');
        }
    }
);

Route::filter(
    'isVolunteerMentorOrAdmin',
    function ($route, $request) {
        $isAllowed = Auth::getUser()->isVolunteerMentorOrAdmin();
        if (!$isAllowed) {
            Flash::error("You do not have proper permission to view this page");
            return Redirect::route('login');
        }
    }
);

Route::filter('loggedIn', function()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if($user->isLender()) {
                return Redirect::route('lender:dashboard');
            } elseif ($user->isBorrower()) {
                return Redirect::route('borrower:dashboard');
            } elseif ($user->isAdmin()) {
                return Redirect::route('admin:dashboard');
            }
        }
    }
);
//
//Route::filter(
//    'forceBorrowerLanguage',
//    function ($route, $request) {
//        $url = Request::url();
//        $countryCode = Utility::getCountryCodeByIP();
//        $country = CountryQuery::create()
//            ->findOneByCountryCode($countryCode);
//       // if($country && $country->isBorrowerCountry()) {
//            $languageCode = $country->getLanguageCode();
//            \Session::set('languageCode', $languageCode);
//            \App::setLocale($languageCode);
//            $localizedRoute = getLocalizedRoute($url, $languageCode);
//            return Redirect::to($localizedRoute);
//      //  }
//    }
//);
