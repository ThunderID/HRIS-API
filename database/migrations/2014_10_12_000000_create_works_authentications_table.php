<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksAuthenticationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('works_authentications', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('tmp_auth_group_id')->unsigned()->index();
			$table->integer('organisation_id')->unsigned()->index();
			$table->integer('work_id')->unsigned()->index();
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
		Schema::drop('works_authentications');
	}

}
