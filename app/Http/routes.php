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
	$body 					= new App\Libraries\API();

	$body 					= $body->authorized(array_merge($app->request->input(), $host) );

	$check_body 			= json_decode($body, true);

	if($check_body['status']=='success' && isset($check_body['data']['whoami']))
	{
		$employee 			= new App\ThunderID\EmploymentSystemV1\Http\Controllers\EmployeeController;

		$employee 			= $employee->detail($check_body['data']['whoami']['id']);

		$check_employee 	= json_decode($employee, true);

		if($check_employee['status']=='success')
		{
			$check_body['data']['whoami'] 	= $check_employee['data'];

			return new App\Libraries\JSend('success', (array)$check_body['data']);
		}
	}

	return $body;
});

$app->get('/close/session', function () use ($app) 
{
	$host['HTTP_HOST'] 		= $app->request->server('HTTP_HOST');
	$queryString 			= $app->request->server('QUERY_STRING');
	$queryString 			= $queryString.'&HTTP_HOST='.$host['HTTP_HOST'];

	$body 					= new App\Libraries\API();
	
	$body 					= $body->closeSession($queryString);

	return $body;
});

