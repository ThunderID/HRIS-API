<?php

namespace App\Models;

use App\Models\Observers\AttendanceLogObserver;

/**
 * Used for AttendanceLog Models
 * 
 * @author cmooy
 */
class AttendanceLog extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasProcessLogTrait;

	/**
	 * Global traits used as query builder (plugged scope).
	 *
	 */

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table					= 'attendance_logs';

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	// protected $timestamps			= true;
	
	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates					=	['created_at', 'updated_at', 'deleted_at', 'modified_at', 'settlement_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends					=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 					= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'process_log_id'				,
											'settlement_by'					,
											'modified_by'					,
											'actual_status'					,
											'modified_status'				,
											'margin_start'					,
											'margin_end'					,
											'tolerance_time'				,
											'count_status'					,
											'notes'							,
											'modified_at'					,
											'settlement_at'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'process_log_id'					=> 'exists:process_logs,id',
											'settlement_by'						=> 'exists:persons,id',
											'modified_by'						=> 'exists:persons,id',
											'actual_status'						=> 'in:AS,HC,HB',
											'modified_status'					=> 'in:DN,SS,SL,CN,CB,CI,UL,HB,L,AS,HC,HD,HT,HP',
											'margin_start'						=> 'numeric',
											'margin_end'						=> 'numeric',
											'tolerance_time'					=> 'numeric',
											'count_status'						=> 'numeric',
											'modified_at'						=> 'date_format:"Y-m-d H:i:s"',
											'settlement_at'						=> 'date_format:"Y-m-d H:i:s"',
										];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 * observing model
	 *
	 */	
	public static function boot() 
	{
        parent::boot();
 
        AttendanceLog::observe(new AttendanceLogObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

}
