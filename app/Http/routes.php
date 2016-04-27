<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->post('/authorized/client', function () use ($app) 
{
	$authorized 		= new ThunderID\ThunderOauthSQL\Authorizer(app('request'));

	return $authorized->issueAccessToken();
});

$app->get('/close/session', function () use ($app) 
{
	$authorized 		= new ThunderID\ThunderOauthSQL\Authorizer(app('request'));

	return $authorized->destroySession();
});

$app->get('/logged/in', function () use ($app) 
{
	return view('thunder-oauth-sql::content.login.form');
});