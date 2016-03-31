<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_ORGANISATION', 'mysql_hr_organisations'))->create('organisations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 255);
			$table->string('code', 255);
			$table->string('logo', 255);
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
		Schema::drop('organisations');
	}

}
