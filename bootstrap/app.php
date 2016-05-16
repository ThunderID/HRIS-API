<?php

require_once __DIR__.'/../vendor/autoload.php';

Dotenv::load(__DIR__.'/../');

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

// class_alias('Illuminate\Support\Facades\App', 'App');

$app->withEloquent();

$app->withFacades();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     // Illuminate\Cookie\Middleware\EncryptCookies::class,
//     // Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
//     // Illuminate\Session\Middleware\StartSession::class,
//     // Illuminate\View\Middleware\ShareErrorsFromSession::class,
//     // Laravel\Lumen\Http\Middleware\VerifyCsrfToken::class,
// ]);

$app->routeMiddleware([
    'oauth' 		=> ThunderID\APIHelper\Middleware\OAuthMiddleware::class,
    'oauth-scope'   => ThunderID\APIHelper\Middleware\OAuthScopeMiddleware::class,
    'oauth-su' 	    => ThunderID\APIHelper\Middleware\OAuthSuperUserMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['middleware' => 'oauth', 'namespace' => 'App\ThunderID\OrganisationManagementV1\Http\Controllers'], function ($app) 
{
    require __DIR__.'/../app/ThunderID/OrganisationManagementV1/Http/routes.php';
});

$app->group(['middleware' => 'oauth', 'namespace' => 'App\ThunderID\EmploymentSystemV1\Http\Controllers'], function ($app) 
{   
	require __DIR__.'/../app/ThunderID/EmploymentSystemV1/Http/routes.php';
});

$app->group(['middleware' => 'oauth', 'namespace' => 'App\ThunderID\WorkforceManagementV1\Http\Controllers'], function ($app) 
{   
    require __DIR__.'/../app/ThunderID/WorkforceManagementV1/Http/routes.php';
});

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../app/Http/routes.php';
});

return $app;
