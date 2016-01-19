<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('persons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->string('uniqid', 255);
			$table->string('name', 255);
			$table->string('prefix_title', 255);
			$table->string('suffix_title', 255);
			$table->string('place_of_birth', 255);
			$table->date('date_of_birth');
			$table->enum('gender', ['male', 'female']);
			$table->string('username', 255);
			$table->string('password', 255);
			$table->text('avatar');
			$table->datetime('last_password_updated_at');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'organisation_id', 'name']);
			$table->index(['deleted_at', 'organisation_id', 'uniqid']);
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
