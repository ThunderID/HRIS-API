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
// SCHEDULE
// ------------------------------------------------------------------------------------
$app->get('/{org_id}/calendar/{cal_id}/schedules',
	[
		'uses'				=> 'ScheduleController@index'
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
// EMPLOYEE DOCUMENT
// ------------------------------------------------------------------------------------
$app->get('/{org_id}/employee/{employ_id}/documents',
	[
		'uses'				=> 'EmployeeDocumentController@index'
	]
);

$app->post('/{org_id}/employee/{employ_id}/document/store',
	[
		'uses'				=> 'EmployeeDocumentController@store'
	]
);

$app->delete('/{org_id}/employee/{employ_id}/document/delete/{id}',
	[
		'uses'				=> 'EmployeeDocumentController@delete'
	]
);

// ------------------------------------------------------------------------------------
// EMPLOYEE SCHEDULE
// ------------------------------------------------------------------------------------
$app->get('/{org_id}/employee/{employ_id}/schedules',
	[
		'uses'				=> 'EmployeeScheduleController@index'
	]
);

$app->post('/{org_id}/employee/{employ_id}/schedule/store',
	[
		'uses'				=> 'EmployeeScheduleController@store'
	]
);

$app->delete('/{org_id}/employee/{employ_id}/schedule/delete/{id}',
	[
		'uses'				=> 'EmployeeScheduleController@delete'
	]
);

// ------------------------------------------------------------------------------------
// EMPLOYEE WORKLEAVE LOG
// ------------------------------------------------------------------------------------
$app->get('/{org_id}/employee/{employ_id}/workleaves',
	[
		'uses'				=> 'EmployeeWorkleaveController@index'
	]
);

// ------------------------------------------------------------------------------------
// TRACKER API
// ------------------------------------------------------------------------------------

$app->group(['namespace' => 'App\Http\Controllers\Tracker'], function ($app) 
{
	//save log v.0
	$app->post('/api/activity/logs',
		[
			'uses'				=> 'LogV0Controller@store'
		]
	);

	//login admin v.0 & v.1
	$app->post('/api/tracker/setting',
		[
			'uses'				=> 'LoginController@absence'
		]
	);

	//test route v.0 & v.1
	$app->post('/api/tracker/test',
		[
			'uses'				=> 'TestDeviceController@absence'
		]
	);

	//save log v.1.
	$app->post('/api/tracker/verse3',
		[
			'uses'				=> 'LogV1Controller@store'
		]
	);

	//sync time v.1.
	$app->post('/api/time/test',
		[
			'uses'				=> 'TestDeviceController@absencetime'
		]
	);

	//auto update v.1.
	$app->post('/api/tracker/update',
		[
			'uses'				=> 'UpdateController@updateversion'
		]
	);
});

// ------------------------------------------------------------------------------------
// FP API
// ------------------------------------------------------------------------------------

$app->group(['namespace' => 'App\Http\Controllers\FP'], function ($app) 
{
	//save log v.0
	$app->post('/api/fp/verse3',
		[
			'uses'				=> 'LogV0Controller@store'
		]
	);

	//login admin v.0
	$app->post('/api/fp/setting',
		[
			'uses'				=> 'LoginController@fp'
		]
	);

	//test route v.0
	$app->post('/api/fp/test',
		[
			'uses'				=> 'TestDeviceController@fp'
		]
	);

	//display finger of the day v.0
	$app->post('/api/fp/oftheday',
		[
			'uses'				=> 'FingerController@fingeroftheday'
		]
	);

	//sync master data and local databases v.0
	$app->post('/api/fp/sync',
		[
			'uses'				=> 'FingerController@dbsync'
		]
	);

	//save persons' finger v.0
	$app->post('/api/fp/enroll',
		[
			'uses'				=> 'FingerController@store'
		]
	);

	//remove persons' finger v.0
	$app->post('/api/fp/delete',
		[
			'uses'				=> 'FingerController@destroy'
		]
	);
});

//all organisation it self
$app->post('/authorized/me',
	[
		// 'middleware'		=> 'oauth',
		'uses'				=> 'MeController@index'
	]
);
