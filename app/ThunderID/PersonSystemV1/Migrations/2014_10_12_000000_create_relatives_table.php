<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelativesTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_PERSON', 'mysql_hr_persons'))->create('relatives', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->integer('relative_id')->unsigned()->index();
			$table->string('relationship', 255);
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'person_id', 'relative_id']);
			$table->index(['deleted_at', 'relationship', 'person_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('relatives');
	}

}
