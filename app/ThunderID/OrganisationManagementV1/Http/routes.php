<?php

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

$app->delete('organisation/{org_id}/branch/{branch_id}/chart/delete/{id}',
	[
		'uses'				=> 'ChartController@delete'
	]
);
