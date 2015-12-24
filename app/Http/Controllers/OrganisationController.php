<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrganisationController extends Controller
{
    /**
     * Display all Organisations
     *
     * @return Response
     */
    public function index()
    {
        $result                     = new \App\Models\Organisation;

        if(Input::has('search'))
        {
            $search                 = Input::get('search');
            switch ($search) 
            {
                case 'absencetoday':
                        $result     = $result->with(['absencetoday']);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        
        $result                     = $result->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display a Organisation
     *
     * @return Response
     */
    public function detail($id = null)
    {
        //
        $result                     = \App\Models\Organisation::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Store a Organisation
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('Organisation'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data Organisation.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Organisation Parameter

        // $Organisation                    = Input::get('Organisation');
        if(is_null($Organisation['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $Organisation['description']     = json_decode($Organisation['description'], true);

        $Organisation_rules              =   [
                                            'name'                      => 'required|max:255',
                                            'upc'                       => 'required|max:255|unique:Organisations,upc,'.(!is_null($Organisation['id']) ? $Organisation['id'] : ''),
                                            'slug'                      => 'required|max:255|unique:Organisations,slug,'.(!is_null($Organisation['id']) ? $Organisation['id'] : ''),
                                            'description.description'   => 'required|max:512',
                                            'description.fit'           => 'required|max:512',
                                        ];

        //1a. Get original data
        $Organisation_data               = \App\Models\Organisation::findornew($Organisation['id']);

        //1b. Validate Basic Organisation Parameter
        $validator                  = Validator::make($Organisation, $Organisation_rules);

        if (!$validator->passes())
        {
            $errors->add('Organisation', $validator->errors());
        }
        else
        {
            //if validator passed, save Organisation
            $Organisation['description'] = json_encode($Organisation['description']);

            $Organisation_data           = $Organisation_data->fill($Organisation);

            if(!$Organisation_data->save())
            {
                $errors->add('Organisation', $Organisation_data->getError());
            }
        }
        //End of validate Organisation

        //2. Validate Organisation Varian Parameter
        if(!$errors->count())
        {
            $varian_current_ids         = [];
            foreach ($Organisation['varians'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $varian_data        = \App\Models\Varian::find($value['id']);

                    if($varian_data)
                    {
                        $varian_rules   =   [
                                                'Organisation_id'                => 'required|numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'sku'                       => 'required|max:255|in:'.$varian_data['sku'].'unique:varians,sku,'.(!is_null($varian_data['id']) ? $varian_data['id'] : ''),
                                                'size'                      => 'required|max:255|in:'.$varian_data['size'],
                                            ];

                        $validator      = Validator::make($varian_data['attributes'], $varian_rules);
                    }
                    else
                    {
                        $varian_rules   =   [
                                                'Organisation_id'                => 'numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'sku'                       => 'required|max:255|unique:varians,sku,'.(!is_null($value['id']) ? $value['id'] : ''),
                                                'size'                      => 'required|max:255|',
                                            ];

                        $validator      = Validator::make($value, $varian_rules);
                    }

                    //if there was varian and validator false
                    if ($varian_data && !$validator->passes())
                    {
                        if($value['Organisation_id']!=$Organisation['id'])
                        {
                            $errors->add('Varian', 'Produk dari Varian Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Varian', 'Produk Varian Tidak Valid.');
                        }
                        else
                        {
                            $varian_data                = $varian_data->fill($value);

                            if(!$varian_data->save())
                            {
                                $errors->add('Varian', $varian_data->getError());
                            }
                            else
                            {
                                $varian_current_ids[]   = $varian_data['id'];
                            }
                        }
                    }
                    //if there was varian and validator false
                    elseif (!$varian_data && !$validator->passes())
                    {
                        $errors->add('Varian', $validator->errors());
                    }
                    elseif($varian_data && $validator->passes())
                    {
                        $varian_current_ids[]           = $varian_data['id'];
                    }
                    else
                    {
                        $value['Organisation_id']            = $Organisation_data['id'];

                        $varian_data                    = new \App\Models\Varian;

                        $varian_data                    = $varian_data->fill($value);

                        if(!$varian_data->save())
                        {
                            $errors->add('Varian', $varian_data->getError());
                        }
                        else
                        {
                            $varian_current_ids[]       = $varian_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $varians                            = \App\Models\Varian::Organisationid($Organisation['id'])->get()->toArray();
                    
                    $varian_should_be_ids               = [];
                    foreach ($varians as $key => $value) 
                    {
                        $varian_should_be_ids[]         = $value['id'];
                    }

                    $difference_varian_ids              = array_diff($varian_should_be_ids, $varian_current_ids);

                    if($difference_varian_ids)
                    {
                        foreach ($difference_varian_ids as $key => $value) 
                        {
                            $varian_data                = \App\Models\Varian::find($value);

                            if(!$varian_data->delete())
                            {
                                $errors->add('Varian', $varian_data->getError());
                            }
                        }
                    }
                }
            }
        }

        //End of validate Organisation varian

        //3. Validate Organisation Price Parameter
        if(!$errors->count())
        {
            $price_current_ids         = [];
            foreach ($Organisation['prices'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $price_data        = \App\Models\Price::find($value['id']);

                    if($price_data)
                    {
                        $price_rules   =   [
                                                'Organisation_id'                => 'required|numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'price'                     => 'required|numeric|in:'.$price_data['price'],
                                                'promo_price'               => 'required|numeric|in:'.$price_data['promo_price'],
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($price_data['attributes'], $price_rules);
                    }
                    else
                    {
                        $price_rules   =   [
                                                'Organisation_id'                => 'numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'price'                     => 'required|numeric',
                                                'promo_price'               => 'required|numeric',
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($value, $price_rules);
                    }

                    //if there was price and validator false
                    if ($price_data && !$validator->passes())
                    {
                        if($value['Organisation_id']!=$Organisation['id'])
                        {
                            $errors->add('Price', 'Produk dari Price Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Price', 'Produk Price Tidak Valid.');
                        }
                        else
                        {
                            $price_data                = $price_data->fill($value);

                            if(!$price_data->save())
                            {
                                $errors->add('Price', $price_data->getError());
                            }
                            else
                            {
                                $price_current_ids[]   = $price_data['id'];
                            }
                        }
                    }
                    //if there was price and validator false
                    elseif (!$price_data && !$validator->passes())
                    {
                        $errors->add('Price', $validator->errors());
                    }
                    elseif($price_data && $validator->passes())
                    {
                        $price_current_ids[]           = $price_data['id'];
                    }
                    else
                    {
                        $value['Organisation_id']           = $Organisation_data['id'];

                        $price_data                    = new \App\Models\Price;

                        $price_data                    = $price_data->fill($value);

                        if(!$price_data->save())
                        {
                            $errors->add('Price', $price_data->getError());
                        }
                        else
                        {
                            $price_current_ids[]       = $price_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $prices                            = \App\Models\Price::Organisationid($Organisation['id'])->get()->toArray();
                    
                    $price_should_be_ids               = [];
                    foreach ($prices as $key => $value) 
                    {
                        $price_should_be_ids[]         = $value['id'];
                    }

                    $difference_price_ids              = array_diff($price_should_be_ids, $price_current_ids);

                    if($difference_price_ids)
                    {
                        foreach ($difference_price_ids as $key => $value) 
                        {
                            $price_data                = \App\Models\Price::find($value);

                            if(!$price_data->delete())
                            {
                                $errors->add('Price', $price_data->getError());
                            }
                        }
                    }
                }
            }
        }
        //End of validate Organisation price

        //4. Validate Organisation Category Parameter
        if(!$errors->count())
        {
            $category_current_ids               = [];

            foreach ($Organisation['categories'] as $key => $value) 
            {
                $category                       = \App\Models\Category::find($value['id']);

                if($category)
                {
                    $category_current_ids[]     = $value['id'];
                }
                else
                {
                    $errors->add('Category', 'Kategori tidak valid.');
                }
            }

            if($errors->count())
            {
                if(!$Organisation_data->categories()->sync($category_current_ids))
                {
                    $errors->add('Category', 'Kategori produk tidak tersimpan.');
                }
            }
        }
        //End of validate Organisation category

        //5. Validate Organisation Tag Parameter
        if(!$errors->count())
        {
            $tag_current_ids                = [];

            foreach ($Organisation['tags'] as $key => $value) 
            {
                $tag                        = \App\Models\Tag::find($value['id']);

                if($tag)
                {
                    $tag_current_ids[]      = $value['id'];
                }
                else
                {
                    $errors->add('Tag', 'Tag tidak valid.');
                }
            }

            if($errors->count())
            {
                if(!$Organisation_data->categories()->sync($tag_current_ids))
                {
                    $errors->add('Tag', 'Tag produk tidak tersimpan.');
                }
            }
        }
        //End of validate Organisation category

        //6. Validate Organisation Label Parameter
        if(!$errors->count())
        {
            $label_current_ids         = [];
            foreach ($Organisation['labels'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $label_data        = \App\Models\OrganisationLabel::find($value['id']);

                    if($label_data)
                    {
                        $label_rules   =   [
                                                'Organisation_id'                => 'required|numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'lable'                     => 'required|max:255|in:'.$label_data['lable'],
                                                'value'                     => 'required|in:'.$label_data['value'],
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"in:'.$label_data['started_at'],
                                                'ended_at'                  => 'date_format:"Y-m-d H:i:s"|in:'.$label_data['ended_at'],
                                            ];

                        $validator      = Validator::make($label_data['attributes'], $label_rules);
                    }
                    else
                    {
                        $label_rules   =   [
                                                'Organisation_id'                => 'numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'lable'                     => 'required|max:255',
                                                'value'                     => 'required',
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                                'ended_at'                  => 'date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($value, $label_rules);
                    }

                    //if there was label and validator false
                    if ($label_data && !$validator->passes())
                    {
                        if($value['Organisation_id']!=$Organisation['id'])
                        {
                            $errors->add('OrganisationLabel', 'Produk dari OrganisationLabel Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('OrganisationLabel', 'Produk OrganisationLabel Tidak Valid.');
                        }
                        else
                        {
                            $label_data                = $label_data->fill($value);

                            if(!$label_data->save())
                            {
                                $errors->add('OrganisationLabel', $label_data->getError());
                            }
                            else
                            {
                                $label_current_ids[]   = $label_data['id'];
                            }
                        }
                    }
                    //if there was label and validator false
                    elseif (!$label_data && !$validator->passes())
                    {
                        $errors->add('OrganisationLabel', $validator->errors());
                    }
                    elseif($label_data && $validator->passes())
                    {
                        $label_current_ids[]           = $label_data['id'];
                    }
                    else
                    {
                        $value['Organisation_id']            = $Organisation_data['id'];

                        $label_data                    = new \App\Models\OrganisationLabel;

                        $label_data                    = $label_data->fill($value);

                        if(!$label_data->save())
                        {
                            $errors->add('OrganisationLabel', $label_data->getError());
                        }
                        else
                        {
                            $label_current_ids[]       = $label_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $labels                            = \App\Models\OrganisationLabel::Organisationid($Organisation['id'])->get()->toArray();
                    
                    $label_should_be_ids               = [];
                    foreach ($labels as $key => $value) 
                    {
                        $label_should_be_ids[]         = $value['id'];
                    }

                    $difference_label_ids              = array_diff($label_should_be_ids, $label_current_ids);

                    if($difference_label_ids)
                    {
                        foreach ($difference_label_ids as $key => $value) 
                        {
                            $label_data                = \App\Models\OrganisationLabel::find($value);

                            if(!$label_data->delete())
                            {
                                $errors->add('OrganisationLabel', $label_data->getError());
                            }
                        }
                    }
                }
            }
        }
        //End of validate Organisation label

        //7. Validate Organisation Image Parameter
        if(!$errors->count())
        {
            $label_current_ids         = [];
            foreach ($Organisation['images'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $image_data        = \App\Models\Image::find($value['id']);

                    if($image_data)
                    {
                        $image_rules   =   [
                                                'imageable_id'              => 'required|numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'imageable_type'            => 'required|max:255|in:'.$image_data['imageable_type'],
                                                'thumbnail'                 => 'required|max:255|in:'.$image_data['thumbnail'],
                                                'image_xs'                  => 'required|max:255|in:'.$image_data['image_xs'],
                                                'image_sm'                  => 'required|max:255|in:'.$image_data['image_sm'],
                                                'image_md'                  => 'required|max:255|in:'.$image_data['image_md'],
                                                'image_lg'                  => 'required|max:255|in:'.$image_data['image_lg'],
                                                'is_default'                => 'boolean|in:'.$image_data['is_default'],
                                            ];

                        $validator      = Validator::make($image_data['attributes'], $image_rules);
                    }
                    else
                    {
                        $image_rules   =   [
                                                'imageable_id'              => 'numeric|'.($is_new ? '' : 'in:'.$Organisation_data['id']),
                                                'thumbnail'                 => 'required|max:255',
                                                'image_xs'                  => 'required|max:255',
                                                'image_sm'                  => 'required|max:255',
                                                'image_md'                  => 'required|max:255',
                                                'image_lg'                  => 'required|max:255',
                                                'is_default'                => 'boolean',
                                            ];

                        $validator      = Validator::make($value, $image_rules);
                    }

                    //if there was image and validator false
                    if ($image_data && !$validator->passes())
                    {
                        if($value['imageable_id']!=$Organisation['id'])
                        {
                            $errors->add('Image', 'Produk dari Image Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Image', 'Produk Image Tidak Valid.');
                        }
                        else
                        {
                            $image_data                = $image_data->fill($value);

                            if(!$image_data->save())
                            {
                                $errors->add('Image', $image_data->getError());
                            }
                            else
                            {
                                $image_current_ids[]   = $image_data['id'];
                            }
                        }
                    }
                    //if there was image and validator false
                    elseif (!$image_data && !$validator->passes())
                    {
                        $errors->add('Image', $validator->errors());
                    }
                    elseif($image_data && $validator->passes())
                    {
                        $image_current_ids[]            = $image_data['id'];
                    }
                    else
                    {
                        $value['imageable_id']          = $Organisation_data['id'];
                        $value['imageable_type']        = get_class($Organisation_data);

                        $image_data                     = new \App\Models\Image;

                        $image_data                     = $image_data->fill($value);

                        if(!$image_data->save())
                        {
                            $errors->add('Image', $image_data->getError());
                        }
                        else
                        {
                            $image_current_ids[]       = $image_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $images                            = \App\Models\Image::imageableid($Organisation['id'])->get()->toArray();
                    
                    $image_should_be_ids               = [];
                    foreach ($images as $key => $value) 
                    {
                        $image_should_be_ids[]         = $value['id'];
                    }

                    $difference_image_ids              = array_diff($image_should_be_ids, $image_current_ids);

                    if($difference_image_ids)
                    {
                        foreach ($difference_image_ids as $key => $value) 
                        {
                            $image_data                = \App\Models\Image::find($value);

                            if(!$image_data->delete())
                            {
                                $errors->add('Image', $image_data->getError());
                            }
                        }
                    }
                }
            }
        }
        //End of validate Organisation image

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_Organisation              = \App\Models\Organisation::id($Organisation_data['id'])->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first()->toArray();

        return new JSend('success', (array)$final_Organisation);
    }

    /**
     * Delete a Organisation
     *
     * @return Response
     */
    public function delete($id = null)
    {
        //
        $Organisation                    = \App\Models\Organisation::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first();

        $result                     = $Organisation;

        if($Organisation->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $Organisation->getError());
    }
}
