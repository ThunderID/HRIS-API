<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('apis', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('branch_id')->unsigned()->index();
			$table->string('client', 255);
			$table->string('secret', 255);
			$table->string('workstation_address', 255);
			$table->string('workstation_name', 255);
			$table->string('tr_version', 255);
			$table->boolean('is_active');
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
		Schema::drop('apis');
	}

}
