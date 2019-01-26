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
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role')->comment('1: admin; 2: user; 3: galon provider; 4: laundry provider; 5: ac provider; 6: cctv provider');
            $table->integer('status')->comment('1: active; 0: not active');
            $table->integer('deposit')->default(0);
            $table->longText('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('business_license_photo')->nullable();
            $table->string('business_place_photo')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
