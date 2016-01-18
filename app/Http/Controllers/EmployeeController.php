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
    public function index($code = null)
    {
        $result                     = new \App\Models\Employee;

        // $result                     = $result->organisationcode($code);
        
        if(Input::has('search'))
        {
            $search                 = Input::get('search');

            foreach ($search as $key => $value) 
            {
                switch (strtolower($key)) 
                {
                    default:
                        # code...
                        break;
                }
            }
        }

        $count                      = $result->count();

        if(Input::has('skip'))
        {
            $skip                   = Input::get('skip');
            $result                 = $result->skip($skip);
        }

        if(Input::has('take'))
        {
            $take                   = Input::get('take');
            $result                 = $result->take($take);
        }

        $result                     = $result->with(['privatedocuments', 'privatedocuments.document', 'privatedocuments.documentdetails', 'privatedocuments.documentdetails.template'])->get()->toArray();

        return new JSend('success', (array)$result);
    }
}
