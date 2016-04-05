<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaritalStatusesTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_PERSON', 'mysql_hr_persons'))->create('marital_statuses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->string('status', 255);
			$table->date('ondate');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'person_id', 'ondate']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('marital_statuses');
	}

}
