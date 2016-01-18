<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Scope to get required document (is_required is true)
 *
 * @return documents
 * @author cmooy
 */

class DocumentRequiredScope implements ScopeInterface  
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
		if(isset($model->required) && $model->required)
		{
			$builder
			->join('tmp_documents', function ($join) 
				 {
	                                    $join->on ( 'tmp_documents.id', '=', 'persons_documents.document_id' )
										->where('is_required', '=', true)
	                                    ->wherenull('persons_documents.deleted_at')
	                                    ;
				})
			;
		}
		else
		{
			$builder
			->join('tmp_documents', function ($join) 
				 {
	                                    $join->on ( 'tmp_documents.id', '=', 'persons_documents.document_id' )
										->where('is_required', '=', false)
	                                    ->wherenull('persons_documents.deleted_at')
	                                    ;
				})
			;
		}

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
	    // unset($query);
	}
}
