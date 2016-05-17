<?php

/**
*
* Routes For Protected Calendar Sub System
*
* Here is where you can register all of the routes for protected resources in Calendar sub system.
*
*/

// ------------------------------------------------------------------------------------
// CALENDARS
// ------------------------------------------------------------------------------------

$app->get('/organisation/{org_id}/calendars',
	[
		'uses'				=> 'CalendarController@index'
	]
);

$app->get('/organisation/{org_id}/calendar/{id}',
	[
		'uses'				=> 'CalendarController@detail'
	]
);

$app->post('/organisation/{org_id}/calendar/store',
	[
		'uses'				=> 'CalendarController@store'
	]
);

$app->delete('/organisation/{org_id}/calendar/delete/{id}',
	[
		'uses'				=> 'CalendarController@delete'
	]
);

// ------------------------------------------------------------------------------------
// SCHEDULES
// ------------------------------------------------------------------------------------
$app->get('/organisation/{org_id}/calendar/{cal_id}/schedules',
	[
		'uses'				=> 'ScheduleController@index'
	]
);

$app->get('/organisation/{org_id}/calendar/{cal_id}/schedule/{id}',
	[
		'uses'				=> 'ScheduleController@detail'
	]
);

$app->post('/organisation/{org_id}/calendar/{cal_id}/schedule/store',
	[
		'uses'				=> 'ScheduleController@store'
	]
);

$app->delete('/organisation/{org_id}/calendar/{cal_id}/schedule/delete/{id}',
	[
		'uses'				=> 'ScheduleController@delete'
	]
);


// ------------------------------------------------------------------------------------
// PERSONSCHEDULES
// ------------------------------------------------------------------------------------
$app->get('/organisation/{org_id}/employee/{emp_id}/schedules',
	[
		'uses'				=> 'EmployeeScheduleController@index'
	]
);

$app->get('/organisation/{org_id}/employee/{emp_id}/schedule/{id}',
	[
		'uses'				=> 'EmployeeScheduleController@detail'
	]
);

$app->post('/organisation/{org_id}/employee/{emp_id}/schedule/store',
	[
		'uses'				=> 'EmployeeScheduleController@store'
	]
);

$app->delete('/organisation/{org_id}/employee/{emp_id}/schedule/delete/{id}',
	[
		'uses'				=> 'EmployeeScheduleController@delete'
	]
);

