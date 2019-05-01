<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up() {
		 Schema::create('users', function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->increments('id')->unsigned();
			$table->string('loginName');
			$table->string('password');
			$table->string('titleName', 90)->nullable();
			$table->string('firstName', 120);
			$table->string('lastname', 140);
			$table->string('idcard', 40)->nullable();
			$table->string('email')->unique();
			$table->string('ref_position', 10)->nullable();
			$table->string('ref_office', 10);
			$table->enum('user_type', ['root', 'admin', 'dev', 'web', 'view'])->default('view');
			$table->enum('user_level', [1, 2, 3, 4, 5])->default(5);
			$table->dateTime('register')->default(NOW());
			$table->rememberToken();
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
		Schema::dropIfExists('users');
	}
}
