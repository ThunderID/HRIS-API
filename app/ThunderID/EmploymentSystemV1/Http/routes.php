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

$app->get('/{org_id}/marital/statuses',
	[
		'uses'				=> 'EmployeeController@maritalstatuses'
	]
);

// ------------------------------------------------------------------------------------
// EMPLOYEE ATTRIBUTE
// ------------------------------------------------------------------------------------
$app->get('/nik/{code}/{id}/{join_year}',
	[
		'uses'				=> 'EmploymentAttributeController@generateNIK'
	]
);

$app->get('/username/{code}/{name}',
	[
		'uses'				=> 'EmploymentAttributeController@generateUsername'
	]
);

$app->get('/document/templates',
	[
		'uses'				=> 'EmploymentAttributeController@getDocumentTemplate'
	]
);

// ------------------------------------------------------------------------------------
// EMPLOYEE ACCOUNT ACTIVATION
// ------------------------------------------------------------------------------------
$app->get('/employee/activation/{activation_link}',
	[
		'uses'				=> 'EmploymentAccountController@getByActivationLink',
		'as'				=> 'employee.activate.link', function ($id) {
		}
	]
);

$app->get('/employee/resend/activation/{id}',
	[
		'uses'				=> 'EmploymentAccountController@resendActivationLink'
	]
);

// ------------------------------------------------------------------------------------
// CONTRACT ELEMENT
// ------------------------------------------------------------------------------------
$app->get('{org_id}/contract/elements',
	[
		'uses'				=> 'ContractElementController@index'
	]
);

$app->post('{org_id}/contract/element/store',
	[
		'uses'				=> 'ContractElementController@store'
	]
);

$app->delete('{org_id}/contract/element/delete/{id}',
	[
		'uses'				=> 'ContractElementController@delete'
	]
);

