<?php

/**
*
* Routes For Protected Employment Sub System
*
* Here is where you can register all of the routes for protected resources in employment sub system.
*
*/

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

$app->get('/{org_id}/grades',
	[
		'uses'				=> 'EmployeeController@grades'
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
