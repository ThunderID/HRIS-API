<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display all employeers of an org
     *
     * @return Response
     */
    public function index()
    {
        $result                     = new \App\Models\Employee;

        if(Input::has('code'))
        {
            $search                 = Input::get('code');

            $result                 = $result->organisationcode($search);
        }
        
        $result                     = $result->quotaworkleave(true)->get()->toArray();

        return new JSend('success', (array)$result);
    }
}
