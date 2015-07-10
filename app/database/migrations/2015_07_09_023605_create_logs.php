<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('logs', function($table){
			$table->increments('id');
			$table->string('table_affected');
			$table->string('column_affected');
			$table->string('action');
			$table->string('old_value')->nullable();
			$table->string('new_value')->nullable();
			$table->string('user');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('logs');
	}

}
