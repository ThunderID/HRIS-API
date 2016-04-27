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

$app->get('/organisation/{org_id}/employees',
	[
		'uses'				=> 'EmployeeController@index'
	]
);

$app->get('/organisation/{org_id}/employee/{id}',
	[
		'uses'				=> 'EmployeeController@detail'
	]
);

$app->post('/organisation/{org_id}/employee/store',
	[
		'uses'				=> 'EmployeeController@store'
	]
);

$app->delete('/organisation/{org_id}/employee/delete/{id}',
	[
		'uses'				=> 'EmployeeController@delete'
	]
);

$app->get('/organisation/{org_id}/grades',
	[
		'uses'				=> 'EmployeeController@grades'
	]
);

$app->get('/organisation/{org_id}/marital/statuses',
	[
		'uses'				=> 'EmployeeController@maritalstatuses'
	]
);

// ------------------------------------------------------------------------------------
// EMPLOYEE ATTRIBUTE
// ------------------------------------------------------------------------------------
$app->get('/organisation/{code}/nik/{id}/{join_year}',
	[
		'uses'				=> 'EmploymentAttributeController@generateNIK'
	]
);

$app->get('/organisation/{code}/username/{id}',
	[
		'uses'				=> 'EmploymentAttributeController@generateUsername'
	]
);

// ------------------------------------------------------------------------------------
// CONTRACT ELEMENT
// ------------------------------------------------------------------------------------
$app->get('/organisation/{org_id}/contract/elements',
	[
		'uses'				=> 'ContractElementController@index'
	]
);

$app->post('/organisation/{org_id}/contract/element/store',
	[
		'uses'				=> 'ContractElementController@store'
	]
);

$app->delete('/organisation/{org_id}/contract/element/delete/{id}',
	[
		'uses'				=> 'ContractElementController@delete'
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

$app->post('/employee/activated/{activation_link}',
	[
		'uses'				=> 'EmploymentAccountController@setPassword',
		'as'				=> 'employee.activated.link', function ($id) {
		}
	]
);

$app->get('/employee/resend/activation/{id}',
	[
		'uses'				=> 'EmploymentAccountController@resendActivationLink'
	]
);

// ------------------------------------------------------------------------------------
// DOCUMENT TEMPLATE
// ------------------------------------------------------------------------------------
$app->get('/document/templates',
	[
		'uses'				=> 'EmploymentAttributeController@getDocumentTemplate'
	]
);

// ------------------------------------------------------------------------------------
// TOOLS
// ------------------------------------------------------------------------------------
$app->get('/import/employee',
	[
		'uses'				=> 'ImportEmployeeController@get'
	]
);

$app->post('/import/employee',
	[
		'uses'				=> 'ImportEmployeeController@post'
	]
);
