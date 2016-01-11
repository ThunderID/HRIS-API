<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpPoliciesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tmp_policies', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->integer('created_by')->unsigned()->index();
			$table->string('type', 255);
			$table->text('value');
			$table->datetime('started_at');
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
		Schema::drop('tmp_policies');
	}

}
