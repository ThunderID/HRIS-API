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
	$host['HTTP_HOST'] 		= $app->request->server('HTTP_HOST');
	$body 					= new ThunderID\APIHelper\API\APIAuth;

	$body 					= $body->loggedIn(array_merge($app->request->input(), $host) );

	// $check_body 			= json_decode($body->, true);

	if($body['status']=='success' && isset($body['data']['whoami']))
	{
		$employee 			= new App\ThunderID\EmploymentSystemV1\Http\Controllers\EmployeeController;

		$employee 			= $employee->detail($body['data']['whoami']['id']);

		$check_employee 	= json_decode($employee, true);

		if($check_employee['status']=='success')
		{
			$body['data']['whoami'] 	= $check_employee['data'];

			return new ThunderID\APIHelper\Data\JSend('success', (array)$body['data']);
		}
	}

	return $body;
});

$app->get('/close/session', function () use ($app) 
{
	$host['HTTP_HOST'] 		= $app->request->server('HTTP_HOST');
	$queryString 			= $app->request->server('QUERY_STRING');
	$queryString 			= $queryString.'&HTTP_HOST='.$host['HTTP_HOST'];

	$body 					= new ThunderID\APIHelper\API\APIAuth;
	
	$body 					= $body->closeSession($queryString);

	return $body;
});

