<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonDocumentsTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_PERSON', 'mysql_hr_persons'))->create('person_documents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->json('documents');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('person_documents');
	}

}
