<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('id from users table');
            $table->integer('ac_vendor_id')->comment('id from users table');
            $table->string('delivered_address');
            $table->string('delivered_lat');
            $table->string('delivered_lng');
            $table->string('order_description')->comment('ex: service');
            $table->integer('status')->comment('0: waiting; 1: accepted; 2: done; -1: canceled');
            $table->string('reason_for_cancel')->nullable();
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
        Schema::dropIfExists('ac_order');
    }
}
