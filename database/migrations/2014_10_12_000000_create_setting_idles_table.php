<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingIdlesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('setting_idles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->integer('created_by')->unsigned()->index();
			$table->date('start');
			$table->double('margin_bottom_idle');
			$table->double('idle_1');
			$table->double('idle_2');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'organisation_id', 'start']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('setting_idles');
	}

}
