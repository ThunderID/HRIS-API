<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MeController extends Controller
{
    /**
     * Display all Organisations
     *
     * @return Response
     */
    public function index()
    {
        $result                     = new \App\Models\Employee;

        if(Input::has('id'))
        {
            $id                     = Input::get('id');

            $result                 = $result->id($id);
        }
        
        $result                     = $result->first()->toArray();

        return new JSend('success', (array)$result);
    }
}
