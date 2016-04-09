<?php

/**
*
* Routes For Protected Organisation Management Sub System
*
* Here is where you can register all of the routes for protected resources in organisation management sub system.
*
*/


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
// BRANCHES
// ------------------------------------------------------------------------------------
$app->get('organisation/{org_id}/branches',
	[
		'uses'				=> 'BranchController@index'
	]
);
$app->get('organisation/{org_id}/branch/{id}',
	[
		'uses'				=> 'BranchController@detail'
	]
);

$app->post('organisation/{org_id}/branch/store',
	[
		'uses'				=> 'BranchController@store'
	]
);

$app->delete('organisation/{org_id}/branch/delete/{id}',
	[
		'uses'				=> 'BranchController@delete'
	]
);

$app->get('organisation/{org_id}/departments',
	[
		'uses'				=> 'ChartController@departments'
	]
);

$app->get('organisation/{org_id}/positions',
	[
		'uses'				=> 'ChartController@positions'
	]
);

// ------------------------------------------------------------------------------------
// CHARTS
// ------------------------------------------------------------------------------------
$app->get('organisation/{org_id}/branch/{branch_id}/charts',
	[
		'uses'				=> 'ChartController@index'
	]
);

$app->post('organisation/{org_id}/branch/{branch_id}/chart/store',
	[
		'uses'				=> 'ChartController@store'
	]
);

$app->get('organisation/{org_id}/branch/{branch_id}/chart/{id}',
	[
		'uses'				=> 'ChartController@detail'
	]
);

$app->delete('organisation/{org_id}/branch/{branch_id}/chart/delete/{id}',
	[
		'uses'				=> 'ChartController@delete'
	]
);

// ------------------------------------------------------------------------------------
// POLICIES
// ------------------------------------------------------------------------------------
$app->get('organisation/{org_id}/policies',
	[
		'uses'				=> 'PolicyController@index'
	]
);

$app->post('organisation/{org_id}/policy/store',
	[
		'uses'				=> 'PolicyController@store'
	]
);

$app->delete('organisation/{org_id}/policy/delete/{id}',
	[
		'uses'				=> 'PolicyController@delete'
	]
);

