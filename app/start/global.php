<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(
    array(
        app_path() . '/commands',
        app_path() . '/controllers',
        app_path() . '/helpers',
        app_path() . '/models',
        app_path() . '/database/seeds',
    )
);

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path() . '/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(
    function (Exception $exception, $code) {
        Log::error($exception);

        if (\Config::get('app.developerEmail')) {
            $adminMailer = App::make('Zidisha\Mail\AdminMailer');
            $adminMailer->sendErrorMail($exception);   
        }
    }
);

App::error(
    function (FacebookApiException $exception) {
        Log::error($exception);
        Flash::error('Facebook error.');
        return Redirect::route('home');
    }
);

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(
    function () {
        return Response::make("Be right back!", 503);
    }
);

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path() . '/filters.php';
/*
 * Setup Propel
 */
if (defined('LARAVEL_ENV')) {
    require app_path() . '/config/propel/config.php';
    if (LARAVEL_ENV == 'local') {
      $con = Propel\Runtime\Propel::getWriteConnection(\Zidisha\User\Map\UserTableMap::DATABASE_NAME);
      Debugbar::addCollector(new DebugBar\DataCollector\PDO\PDOCollector($con->getConnection()));
    }
}


/*
 * Extending auth class with propel
 */
Auth::extend(
    'propel',
    function ($app) {
        return new \Zidisha\Auth\PropelUserProvider();
    }
);

/**
 * Custom validator class
 */

Validator::resolver(function($translator, $data, $rules, $messages)
{
    return new Zidisha\Form\ZidishaValidator($translator, $data, $rules, $messages);
});

if (!function_exists('getLocalizedRoute')) {
    function getLocalizedRoute ($url, $languageCode) {
        $url = parse_url($url);
        $newUrl = null;
        if (array_get($url, 'scheme')) {
            $newUrl = $url['scheme'] .'://';
        }
        if (array_get($url, 'host')) {
            $newUrl .= $url['host'] .':';
        }
        if (array_get($url, 'port')) {
            $newUrl .=  $url['port'] .'/';
        }
        if (array_get($url, 'path')) {
            $parts = explode('/', $url['path']);
            if (in_array($parts[0], ['fr', 'in', 'en'])) {
                if ($parts[0] != $languageCode) {
                    unset($parts[0]);
                    $newUrl .= $languageCode . '/' . implode('/', $parts);
                }
            } else {
                $newUrl .= $languageCode . '/' . $url['path'];
            }
        } else {
            $newUrl .= $languageCode . '/' . $url['path'];
        }

        return $newUrl;
    }
}
