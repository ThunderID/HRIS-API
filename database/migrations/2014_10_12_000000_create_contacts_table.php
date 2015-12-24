<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contacts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('branch_id')->unsigned()->index();
			$table->integer('person_id')->unsigned()->index();
			$table->string('item', 255);
			$table->text('value');
			$table->string('branch_type', 255);
			$table->string('person_type', 255);
			$table->boolean('is_default');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at' ,'is_default', 'branch_id', 'item']);
			$table->index(['deleted_at' ,'is_default', 'person_id', 'item']);
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
