<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration 
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_ORGANISATION', 'mysql_hr_organisations'))->create('contacts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contactable_id')->unsigned()->index();
			$table->string('contactable_type', 255);
			$table->string('item', 255);
			$table->text('value');
			$table->boolean('is_default');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at' ,'contactable_type', 'item', 'is_default']);
			$table->index(['deleted_at' ,'contactable_type', 'is_default']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contacts');
	}

}
