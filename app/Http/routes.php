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

$app->get('/', function () use ($app) {
    return $app->welcome();
});

// ------------------------------------------------------------------------------------
// ORGANISATIONS
// ------------------------------------------------------------------------------------
$app->get('/organisations',
	[
		'uses'				=> 'OrganisationController@index'
	]
);

$app->get('/organisation/{id}',
	[
		'uses'				=> 'OrganisationController@detail'
	]
);

$app->post('/organisation/store',
	[
		'uses'				=> 'OrganisationController@store'
	]
);

$app->get('/organisation/delete/{id}',
	[
		'uses'				=> 'OrganisationController@delete'
	]
);


// ------------------------------------------------------------------------------------
// EMPLOYEES
// ------------------------------------------------------------------------------------
$app->get('/employees/{org_id}',
	[
		'uses'				=> 'EmployeeController@index'
	]
);

$app->get('/employee/{id}',
	[
		'uses'				=> 'EmployeeController@detail'
	]
);

$app->post('/employee/store',
	[
		'uses'				=> 'EmployeeController@store'
	]
);

$app->delete('/employee/delete/{id}',
	[
		'uses'				=> 'EmployeeController@delete'
	]
);
// ------------------------------------------------------------------------------------
// ME
// ------------------------------------------------------------------------------------

//all organisation it self
$app->post('/authorized/me',
	[
		// 'middleware'		=> 'oauth',
		'uses'				=> 'MeController@index'
	]
);
