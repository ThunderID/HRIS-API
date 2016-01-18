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
        $result                     = \App\Models\Organisation::id($id)->with(['branches', 'calendars', 'calendars', 'workleaves', 'documents', 'documents.templates', 'policies'])->first();

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
     * 5. store document
     * 6. store policy
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
                                            'code'                      => 'required|max:255|unique:organisations,code,'.(!is_null($organisation['id']) ? $organisation['id'] : ''),
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
        if(!$errors->count() && isset($organisation['branches']) && is_array($organisation['branches']))
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
                $branches                            = \App\Models\Branch::organisationid($organisation['id'])->get()->toArray();
                
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
        if(!$errors->count() && isset($organisation['calendars']) && is_array($organisation['calendars']))
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
                $calendars                            = \App\Models\Calendar::organisationid($organisation['id'])->get()->toArray();
                
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
        if(!$errors->count() && isset($organisation['workleaves']) && is_array($organisation['workleaves']))
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
                $workleaves                            = \App\Models\Workleave::organisationid($organisation['id'])->get()->toArray();
                
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

        //5. Validate Organisation document Parameter
        if(!$errors->count() && isset($organisation['documents']) && is_array($organisation['documents']))
        {
            $document_current_ids         = [];
            foreach ($organisation['documents'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $document_data        = \App\Models\Document::find($value['id']);

                    if($document_data)
                    {
                        $document_rules   = [
                                                'organisation_id'           => 'required|numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'name'                      => 'required|max:255',
                                                'tag'                       => 'required|max:255',
                                                'template'                  => 'required',
                                                'is_required'               => 'boolean',
                                            ];

                        $validator      = Validator::make($document_data['attributes'], $document_rules);
                    }
                    else
                    {
                        $document_rules   =   [
                                                'organisation_id'           => 'numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'name'                      => 'required|max:255',
                                                'quota'                     => 'required|max:255',
                                                'template'                  => 'required',
                                                'is_required'               => 'boolean',
                                            ];

                        $validator      = Validator::make($value, $document_rules);
                    }

                    //if there was document and validator false
                    if ($document_data && !$validator->passes())
                    {
                        if($value['organisation_id']!=$organisation['id'])
                        {
                            $errors->add('document', 'Organisasi dari document Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('document', 'Organisasi document Tidak Valid.');
                        }
                        else
                        {
                            $document_data                = $document_data->fill($value);

                            if(!$document_data->save())
                            {
                                $errors->add('document', $document_data->getError());
                            }
                            else
                            {
                                $document_current_ids[]   = $document_data['id'];
                            }
                        }
                    }
                    //if there was document and validator false
                    elseif (!$document_data && !$validator->passes())
                    {
                        $errors->add('document', $validator->errors());
                    }
                    elseif($document_data && $validator->passes())
                    {
                        $document_current_ids[]           = $document_data['id'];
                    }
                    else
                    {
                        $value['organisation_id']            = $organisation_data['id'];

                        $document_data                    = new \App\Models\Document;

                        $document_data                    = $document_data->fill($value);

                        if(!$document_data->save())
                        {
                            $errors->add('document', $document_data->getError());
                        }
                        else
                        {
                            $document_current_ids[]       = $document_data['id'];
                        }
                    }
                }

                //save templates
                if(!$errors->count() && isset($product['templates']) && is_array($product['templates']))
                {
                    $template_current_ids         = [];
                    foreach ($value['templates'] as $key2 => $value2) 
                    {
                        if(!$errors->count())
                        {
                            $template_data        = \App\Models\Template::find($value2['id']);

                            if($template_data)
                            {
                                $template_rules   = [
                                                        'document_id'               => 'required|numeric|'.($is_new ? '' : 'in:'.$value['id']),
                                                        'field'                     => 'required|max:255',
                                                        'type'                      => 'required|max:255',
                                                    ];

                                $validator      = Validator::make($template_data['attributes'], $template_rules);
                            }
                            else
                            {
                                $template_rules   =   [
                                                        'document_id'               => 'numeric|'.($is_new ? '' : 'in:'.$value['id']),
                                                        'field'                     => 'required|max:255',
                                                        'type'                      => 'required|max:255',
                                                    ];

                                $validator      = Validator::make($value2, $template_rules);
                            }

                            //if there was template and validator false
                            if ($template_data && !$validator->passes())
                            {
                                if($value2['document_id']!=$value['id'])
                                {
                                    $errors->add('template', 'Dokumen dari template Tidak Valid.');
                                }
                                elseif($is_new)
                                {
                                    $errors->add('template', 'Dokumen template Tidak Valid.');
                                }
                                else
                                {
                                    $template_data                = $template_data->fill($value2);

                                    if(!$template_data->save())
                                    {
                                        $errors->add('template', $template_data->getError());
                                    }
                                    else
                                    {
                                        $template_current_ids[]   = $template_data['id'];
                                    }
                                }
                            }
                            //if there was template and validator false
                            elseif (!$template_data && !$validator->passes())
                            {
                                $errors->add('template', $validator->errors());
                            }
                            elseif($template_data && $validator->passes())
                            {
                                $template_current_ids[]           = $template_data['id'];
                            }
                            else
                            {
                                $value2['document_id']            = $value['id'];

                                $template_data                    = new \App\Models\Template;

                                $template_data                    = $template_data->fill($value2);

                                if(!$template_data->save())
                                {
                                    $errors->add('template', $template_data->getError());
                                }
                                else
                                {
                                    $template_current_ids[]       = $template_data['id'];
                                }
                            }
                        }
                    }
                    //if there was no error, check if there were things need to be delete
                    if(!$errors->count())
                    {
                        $templates                            = \App\Models\Template::documentid($value['id'])->get()->toArray();
                        
                        $template_should_be_ids               = [];
                        foreach ($templates as $key2 => $value2) 
                        {
                            $template_should_be_ids[]         = $value2['id'];
                        }

                        $difference_template_ids              = array_diff($template_should_be_ids, $template_current_ids);

                        if($difference_template_ids)
                        {
                            foreach ($difference_template_ids as $key2 => $value2) 
                            {
                                $template_data                = \App\Models\Template::find($value2);

                                if(!$template_data->delete())
                                {
                                    $errors->add('template', $template_data->getError());
                                }
                            }
                        }
                    }
                }
            }
            //if there was no error, check if there were things need to be delete
            if(!$errors->count())
            {
                $documents                            = \App\Models\Document::organisationid($organisation['id'])->get()->toArray();
                
                $document_should_be_ids               = [];
                foreach ($documents as $key => $value) 
                {
                    $document_should_be_ids[]         = $value['id'];
                }

                $difference_document_ids              = array_diff($document_should_be_ids, $document_current_ids);

                if($difference_document_ids)
                {
                    foreach ($difference_document_ids as $key => $value) 
                    {
                        $document_data                = \App\Models\Document::find($value);

                        if(!$document_data->delete())
                        {
                            $errors->add('document', $document_data->getError());
                        }
                    }
                }
            }
        }
        //End of validate Organisation document

        //6. Validate Organisation Policy Parameter
        if(!$errors->count() && isset($organisation['policies']) && is_array($organisation['policies']))
        {
            $policy_current_ids         = [];
            foreach ($organisation['policies'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $policy_data        = \App\Models\Policy::find($value['id']);

                    if($policy_data)
                    {
                        $policy_rules   = [
                                                'organisation_id'           => 'required|numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'type'                      => 'required|max:255',
                                                'value'                     => 'required',
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($policy_data['attributes'], $policy_rules);
                    }
                    else
                    {
                        $policy_rules   =   [
                                                'organisation_id'           => 'numeric|'.($is_new ? '' : 'in:'.$organisation_data['id']),
                                                'type'                      => 'required|max:255',
                                                'value'                     => 'required',
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($value, $policy_rules);
                    }

                    //if there was Policy and validator false
                    if ($policy_data && !$validator->passes())
                    {
                        if($value['organisation_id']!=$organisation['id'])
                        {
                            $errors->add('Policy', 'Organisasi dari Policy Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Policy', 'Organisasi Policy Tidak Valid.');
                        }
                        else
                        {
                            $policy_data                = $policy_data->fill($value);

                            if(!$policy_data->save())
                            {
                                $errors->add('Policy', $policy_data->getError());
                            }
                            else
                            {
                                $policy_current_ids[]   = $policy_data['id'];
                            }
                        }
                    }
                    //if there was Policy and validator false
                    elseif (!$policy_data && !$validator->passes())
                    {
                        $errors->add('Policy', $validator->errors());
                    }
                    elseif($policy_data && $validator->passes())
                    {
                        $policy_current_ids[]           = $policy_data['id'];
                    }
                    else
                    {
                        $value['organisation_id']            = $organisation_data['id'];

                        $policy_data                    = new \App\Models\Policy;

                        $policy_data                    = $policy_data->fill($value);

                        if(!$policy_data->save())
                        {
                            $errors->add('Policy', $policy_data->getError());
                        }
                        else
                        {
                            $policy_current_ids[]       = $policy_data['id'];
                        }
                    }
                }
            }
            //if there was no error, check if there were things need to be delete
            if(!$errors->count())
            {
                $policies                               = \App\Models\Policy::organisationid($organisation['id'])->get()->toArray();
                
                $policy_should_be_ids                   = [];
                foreach ($policies as $key => $value) 
                {
                    $policy_should_be_ids[]             = $value['id'];
                }

                $difference_policy_ids                  = array_diff($policy_should_be_ids, $policy_current_ids);

                if($difference_policy_ids)
                {
                    foreach ($difference_policy_ids as $key => $value) 
                    {
                        $policy_data                    = \App\Models\Policy::find($value);

                        if(!$policy_data->delete())
                        {
                            $errors->add('Policy', $policy_data->getError());
                        }
                    }
                }
            }
        }
        //End of validate Organisation Policy
        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_organisation             = \App\Models\Organisation::id($organisation_data['id'])->with(['branches', 'calendars', 'calendars.schedules', 'workleaves', 'documents', 'documents.templates'])->first()->toArray();

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
        $organisation               = \App\Models\Organisation::id($id)->with(['branches', 'calendars', 'calendars.schedules', 'workleaves', 'documents', 'documents.templates'])->first();

        $result                     = $organisation;

        if($organisation->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $organisation->getError());
    }
}
