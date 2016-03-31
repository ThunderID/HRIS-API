<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Scopes\GlobalScope;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to get default contact of branch/person
 *
 * @return phone, address, email
 * @author cmooy
 */
class ContactScope implements ScopeInterface  
{
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		// if(!is_null($model::custom_prefix))
		// {
		// 	$prefix		= $model::custom_prefix;
		// }
		// else
		// {
			$prefix 	= \DB::getTablePrefix();
		// }

		$prefix_contact	= \DB::getTablePrefix();

		$builder
				->selectraw('(SELECT IFNULL(value, "not available") from '.$prefix_contact.'contacts as contacts where contacts.contactable_id = '.$prefix.$model->getTable().'.id and contacts.contactable_type like "%'.class_basename($model).'" and type = "address" and is_default = 1 and contacts.deleted_at is null) as address')
				->selectraw('(SELECT IFNULL(value, "not available") from '.$prefix_contact.'contacts as contacts where contacts.contactable_id = '.$prefix.$model->getTable().'.id and contacts.contactable_type like "%'.class_basename($model).'" and type = "phone" and is_default = 1 and contacts.deleted_at is null) as phone')
				->selectraw('(SELECT IFNULL(value, "not available") from '.$prefix_contact.'contacts as contacts where contacts.contactable_id = '.$prefix.$model->getTable().'.id and contacts.contactable_type like "%'.class_basename($model).'" and type = "email" and is_default = 1 and contacts.deleted_at is null) as email')
				->groupby($model->getTable().'.id')
				;
	}

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function remove(Builder $builder, Model $model)
	{
		$query = $builder->getQuery();
		// unset($query->wheres['Employee']);
	}
}
