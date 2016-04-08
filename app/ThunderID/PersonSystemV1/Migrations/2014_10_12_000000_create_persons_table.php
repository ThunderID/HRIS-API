<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_PERSON', 'mysql_hr_persons'))->create('persons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 255);
			$table->string('username', 255);
			$table->string('prefix_title', 255);
			$table->string('suffix_title', 255);
			$table->string('place_of_birth', 255);
			$table->datetime('date_of_birth');
			$table->enum('gender', ['male', 'female']);
			$table->string('password', 60);
			$table->text('avatar');
			$table->string('activation_link', 255);
			$table->datetime('last_logged_at');
			$table->datetime('last_password_updated_at');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'username']);
			$table->index(['deleted_at', 'name']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('persons');
	}

}
