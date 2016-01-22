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

$app->delete('/organisation/delete/{id}',
	[
		'uses'				=> 'OrganisationController@delete'
	]
);


// ------------------------------------------------------------------------------------
// EMPLOYEES
// ------------------------------------------------------------------------------------
$app->get('/{org_id}/employees',
	[
		'uses'				=> 'EmployeeController@index'
	]
);

$app->get('/{org_id}/employee/{id}',
	[
		'uses'				=> 'EmployeeController@detail'
	]
);

$app->post('/{org_id}/employee/store',
	[
		'uses'				=> 'EmployeeController@store'
	]
);

$app->delete('/{org_id}/employee/delete/{id}',
	[
		'uses'				=> 'EmployeeController@delete'
	]
);

// ------------------------------------------------------------------------------------
// BRANCH
// ------------------------------------------------------------------------------------
$app->get('/{org_id}/branch/{id}',
	[
		'uses'				=> 'BranchController@detail'
	]
);

$app->post('/{org_id}/branch/store',
	[
		'uses'				=> 'BranchController@store'
	]
);

$app->delete('/{org_id}/branch/delete/{id}',
	[
		'uses'				=> 'BranchController@delete'
	]
);

// ------------------------------------------------------------------------------------
// SCHEDULE
// ------------------------------------------------------------------------------------
$app->get('/{org_id}/calendar/{cal_id}/schedules',
	[
		'uses'				=> 'ScheduleController@index'
	]
);

$app->get('/{org_id}/calendar/{cal_id}/schedule/{id}',
	[
		'uses'				=> 'ScheduleController@detail'
	]
);

$app->post('/{org_id}/calendar/{cal_id}/schedule/store',
	[
		'uses'				=> 'ScheduleController@store'
	]
);

$app->delete('/{org_id}/calendar/{cal_id}/schedule/delete/{id}',
	[
		'uses'				=> 'ScheduleController@delete'
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
