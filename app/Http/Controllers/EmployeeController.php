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
    public function index($org_id = null)
    {
        $result                     = new \App\Models\Employee;

        $result                     = $result->organisationid($org_id);
        
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

    /**
     * Display all employeers of an org
     *
     * @return Response
     */
    public function detail($org_id = null, $id = null)
    {
        $result                     = \App\Models\Employee::id($id)->organisationid($org_id)->with(['privatedocuments', 'privatedocuments.document', 'privatedocuments.documentdetails', 'privatedocuments.documentdetails.template', 'careers', 'workexperiences', 'maritalstatuses', 'contacts'])->first();

        if($result)
        {
            return new JSend('success', (array)$result->toArray());
        }

        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }
}
