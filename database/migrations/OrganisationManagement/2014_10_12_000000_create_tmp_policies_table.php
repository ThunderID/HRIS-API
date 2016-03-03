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
		Schema::connection(env('DB_CONNECTION_ORGANISATION', 'mysql_hr_organisations'))->create('tmp_policies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->string('code', 255);
			$table->string('name', 255);
			$table->text('parameter');
			$table->text('action');
			$table->text('description');
			$table->datetime('started_at');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'organisation_id', 'code' ,'started_at']);
			$table->index(['deleted_at', 'code' ,'started_at']);
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
