<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone_number', 13)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('sex', 50)->nullable();
            $table->mediumText('description')->nullable();
            $table->string('image_profile', 255)->nullable();
            $table->string('image_main', 255)->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
