<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractWorksTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_EMPLOYMENT', 'mysql_hr_employments'))->create('contracts_works', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contract_element_id')->unsigned()->index();
			$table->integer('work_id')->unsigned()->index();
			$table->string('value', 255);
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'work_id']);
			$table->index(['deleted_at', 'value']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contracts_works');
	}

}
