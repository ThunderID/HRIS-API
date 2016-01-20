<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
	/**
	 * Display a branch of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $id = null)
	{
		$result						= \App\Models\Branch::id($id)->organisationid($org_id)->with(['charts', 'charts.follows', 'charts.follows.calendar', 'charts.chartworkleaves', 'charts.chartworkleaves.workleave', 'contacts', 'apis', 'fingerprint'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}
}
