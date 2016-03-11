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
			$table->string('status', 255);
			$table->date('ondate');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'ondate', 'status']);
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
