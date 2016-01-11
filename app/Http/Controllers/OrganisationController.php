<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of organisation
 * 
 * @author cmooy
 */
class OrganisationController extends Controller
{
    /**
     * Display all Organisations
     *
     * @param search, skip, take
     * @return JSend Response
     */
    public function index()
    {
        $result                     = new \App\Models\Organisation;

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

        $result                     = $result->with(['branches'])->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }

    /**
     * Display an Organisation
     *
     * @param id
     * @return Response
     */
    public function detail($id = null)
    {
        //
        $result                     = \App\Models\Organisation::id($id)->with(['branches', 'calendars', 'calendars.schedules', 'workleaves'])->first();

        if($result)
        {
            return new JSend('success', (array)$result->toArray());
        }
        
        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }

    /**
     * Store an organisation
     * 1. store organisation
     * 2. store branch
     * 3. store calendar
     * 4. store workleave
     *
     * @param organisation
     * @return Response
     */
    public function store()
    {
        if(!Input::has('organisation'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data Organisation.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Organisation Parameter
        $organisation                    = Input::get('organisation');

        if(is_null($organisation['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $organisation_rules         =   [
                                            'name'                      => 'required|max:255',
                                            'code'                      => 'required|max:255|unique:organisations,upc,'.(!is_null($organisation['id']) ? $organisation['id'] : ''),
                                        ];

        //1a. Get original data
        $organisation_data          = \App\Models\Organisation::findornew($organisation['id']);

        //1b. Validate Basic Organisation Parameter
        $validator                  = Validator::make($organisation, $organisation_rules);

        if (!$validator->passes())
        {
            $errors->add('Organisation', $validator->errors());
        }
        else
        {
            //if validator passed, save Organisation
            $organisation_data           = $organisation_data->fill($organisation);

            if(!$organisation_data->save())
            {
                $errors->add('Organisation', $organisation_data->getError());
            }
        }
        //End of validate Organisation

        //2. Validate Organisation branch Parameter
        if(!$errors->count() && isset($product['branches']) && is_array($product['branches']))
        {
            $branch_current_ids         = [];
            foreach ($organisation['branches'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $branch_data        = \App\Models\Branch::find($value['id']);

                    if($branch_data)
                    {
                        $branch_rules   =   [
                                                'organisation_id'           => 'required|numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'name'                      => 'required|max:255',
                                            ];

                        $validator      = Validator::make($branch_data['attributes'], $branch_rules);
                    }
                    else
                    {
                        $branch_rules   =   [
                                                'organisation_id'           => 'numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'name'                      => 'required|max:255',
                                            ];

                        $validator      = Validator::make($value, $branch_rules);
                    }

                    //if there was branch and validator false
                    if ($branch_data && !$validator->passes())
                    {
                        if($value['organisation_id']!=$organisation['id'])
                        {
                            $errors->add('branch', 'Organisasi dari branch Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('branch', 'Organisasi branch Tidak Valid.');
                        }
                        else
                        {
                            $branch_data                = $branch_data->fill($value);

                            if(!$branch_data->save())
                            {
                                $errors->add('branch', $branch_data->getError());
                            }
                            else
                            {
                                $branch_current_ids[]   = $branch_data['id'];
                            }
                        }
                    }
                    //if there was branch and validator false
                    elseif (!$branch_data && !$validator->passes())
                    {
                        $errors->add('branch', $validator->errors());
                    }
                    elseif($branch_data && $validator->passes())
                    {
                        $branch_current_ids[]           = $branch_data['id'];
                    }
                    else
                    {
                        $value['organisation_id']            = $organisation_data['id'];

                        $branch_data                    = new \App\Models\Branch;

                        $branch_data                    = $branch_data->fill($value);

                        if(!$branch_data->save())
                        {
                            $errors->add('branch', $branch_data->getError());
                        }
                        else
                        {
                            $branch_current_ids[]       = $branch_data['id'];
                        }
                    }
                }
            }
            //if there was no error, check if there were things need to be delete
            if(!$errors->count())
            {
                $branches                            = \App\Models\Branch::Organisationid($organisation['id'])->get()->toArray();
                
                $branch_should_be_ids               = [];
                foreach ($branches as $key => $value) 
                {
                    $branch_should_be_ids[]         = $value['id'];
                }

                $difference_branch_ids              = array_diff($branch_should_be_ids, $branch_current_ids);

                if($difference_branch_ids)
                {
                    foreach ($difference_branch_ids as $key => $value) 
                    {
                        $branch_data                = \App\Models\Branch::find($value);

                        if(!$branch_data->delete())
                        {
                            $errors->add('branch', $branch_data->getError());
                        }
                    }
                }
            }
        }

        //End of validate Organisation branch

        //3. Validate Organisation calendar Parameter
        if(!$errors->count() && isset($product['calendars']) && is_array($product['calendars']))
        {
            $calendar_current_ids         = [];
            foreach ($organisation['calendars'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $calendar_data        = \App\Models\Calendar::find($value['id']);

                    if($calendar_data)
                    {
                        $calendar_rules   =   [
                                                'organisation_id'               => 'required|numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'import_from_id'                => 'required',
                                                'name'                          => 'required|max:255',
                                                'workdays'                      => 'required',
                                                'start'                         => 'required',
                                                'end'                           => 'required',
                                            ];

                        $validator      = Validator::make($calendar_data['attributes'], $calendar_rules);
                    }
                    else
                    {
                        $calendar_rules   =   [
                                                'organisation_id'               => 'numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'import_from_id'                => 'required',
                                                'name'                          => 'required|max:255',
                                                'workdays'                      => 'required',
                                                'start'                         => 'required',
                                                'end'                           => 'required',
                                            ];

                        $validator      = Validator::make($value, $calendar_rules);
                    }

                    //if there was calendar and validator false
                    if ($calendar_data && !$validator->passes())
                    {
                        if($value['organisation_id']!=$organisation['id'])
                        {
                            $errors->add('calendar', 'Organisasi dari calendar Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('calendar', 'Organisasi calendar Tidak Valid.');
                        }
                        else
                        {
                            $calendar_data                = $calendar_data->fill($value);

                            if(!$calendar_data->save())
                            {
                                $errors->add('calendar', $calendar_data->getError());
                            }
                            else
                            {
                                $calendar_current_ids[]   = $calendar_data['id'];
                            }
                        }
                    }
                    //if there was calendar and validator false
                    elseif (!$calendar_data && !$validator->passes())
                    {
                        $errors->add('calendar', $validator->errors());
                    }
                    elseif($calendar_data && $validator->passes())
                    {
                        $calendar_current_ids[]           = $calendar_data['id'];
                    }
                    else
                    {
                        $value['organisation_id']           = $organisation_data['id'];

                        $calendar_data                    = new \App\Models\Calendar;

                        $calendar_data                    = $calendar_data->fill($value);

                        if(!$calendar_data->save())
                        {
                            $errors->add('calendar', $calendar_data->getError());
                        }
                        else
                        {
                            $calendar_current_ids[]       = $calendar_data['id'];
                        }
                    }
                }
            }
            //if there was no error, check if there were things need to be delete
            if(!$errors->count())
            {
                $calendars                            = \App\Models\Calendar::Organisationid($organisation['id'])->get()->toArray();
                
                $calendar_should_be_ids               = [];
                foreach ($calendars as $key => $value) 
                {
                    $calendar_should_be_ids[]         = $value['id'];
                }

                $difference_calendar_ids              = array_diff($calendar_should_be_ids, $calendar_current_ids);

                if($difference_calendar_ids)
                {
                    foreach ($difference_calendar_ids as $key => $value) 
                    {
                        $calendar_data                = \App\Models\Calendar::find($value);

                        if(!$calendar_data->delete())
                        {
                            $errors->add('calendar', $calendar_data->getError());
                        }
                    }
                }
            }
        }
        //End of validate Organisation calendar

        //4. Validate Organisation workleave Parameter
        if(!$errors->count())
        {
            $workleave_current_ids         = [];
            foreach ($organisation['workleaves'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $workleave_data        = \App\Models\Workleave::find($value['id']);

                    if($workleave_data)
                    {
                        $workleave_rules   = [
                                                'organisation_id'           => 'required|numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'name'                      => 'required|max:255',
                                                'quota'                     => 'required|numeric',
                                                'status'                    => 'required|in:CN,CB,CI',
                                                'is_active'                 => 'boolean',
                                            ];

                        $validator      = Validator::make($workleave_data['attributes'], $workleave_rules);
                    }
                    else
                    {
                        $workleave_rules   =   [
                                                'organisation_id'           => 'numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'name'                      => 'required|max:255',
                                                'quota'                     => 'required|numeric',
                                                'status'                    => 'required|in:CN,CB,CI',
                                                'is_active'                 => 'boolean',
                                            ];

                        $validator      = Validator::make($value, $workleave_rules);
                    }

                    //if there was workleave and validator false
                    if ($workleave_data && !$validator->passes())
                    {
                        if($value['organisation_id']!=$organisation['id'])
                        {
                            $errors->add('Workleave', 'Organisasi dari Workleave Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Workleave', 'Organisasi Workleave Tidak Valid.');
                        }
                        else
                        {
                            $workleave_data                = $workleave_data->fill($value);

                            if(!$workleave_data->save())
                            {
                                $errors->add('Workleave', $workleave_data->getError());
                            }
                            else
                            {
                                $workleave_current_ids[]   = $workleave_data['id'];
                            }
                        }
                    }
                    //if there was workleave and validator false
                    elseif (!$workleave_data && !$validator->passes())
                    {
                        $errors->add('Workleave', $validator->errors());
                    }
                    elseif($workleave_data && $validator->passes())
                    {
                        $workleave_current_ids[]           = $workleave_data['id'];
                    }
                    else
                    {
                        $value['organisation_id']            = $organisation_data['id'];

                        $workleave_data                    = new \App\Models\Workleave;

                        $workleave_data                    = $workleave_data->fill($value);

                        if(!$workleave_data->save())
                        {
                            $errors->add('Workleave', $workleave_data->getError());
                        }
                        else
                        {
                            $workleave_current_ids[]       = $workleave_data['id'];
                        }
                    }
                }
            }
            //if there was no error, check if there were things need to be delete
            if(!$errors->count())
            {
                $workleaves                            = \App\Models\Workleave::Organisationid($organisation['id'])->get()->toArray();
                
                $workleave_should_be_ids               = [];
                foreach ($workleaves as $key => $value) 
                {
                    $workleave_should_be_ids[]         = $value['id'];
                }

                $difference_workleave_ids              = array_diff($workleave_should_be_ids, $workleave_current_ids);

                if($difference_workleave_ids)
                {
                    foreach ($difference_workleave_ids as $key => $value) 
                    {
                        $workleave_data                = \App\Models\Workleave::find($value);

                        if(!$workleave_data->delete())
                        {
                            $errors->add('Workleave', $workleave_data->getError());
                        }
                    }
                }
            }
        }
        //End of validate Organisation workleave

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_organisation             = \App\Models\Organisation::id($organisation_data['id'])->with(['branches', 'calendars', 'calendars.schedules', 'workleaves'])->first()->toArray();

        return new JSend('success', (array)$final_organisation);
    }

    /**
     * Delete an Organisation
     *
     * @return Response
     */
    public function delete($id = null)
    {
        //
        $organisation               = \App\Models\Organisation::id($id)->with(['branches', 'calendars', 'calendars.schedules', 'workleaves'])->first();

        $result                     = $organisation;

        if($organisation->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $organisation->getError());
    }
}
