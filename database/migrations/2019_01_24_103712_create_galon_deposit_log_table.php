<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalonDepositLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galon_deposit_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('id from users table as vendor_id');
            $table->integer('amount');
            $table->integer('approved_by')->comment('id from users table as admin_id');
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
        Schema::dropIfExists('galon_deposit_log');
    }
}
