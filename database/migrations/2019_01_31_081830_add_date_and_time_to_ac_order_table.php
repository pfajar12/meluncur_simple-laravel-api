<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateAndTimeToAcOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ac_order', function (Blueprint $table) {
            $table->date('order_date')->after('order_description');
            $table->time('order_time')->after('order_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ac_order', function (Blueprint $table) {
            $table->dropColumn('order_date');
            $table->dropColumn('order_time');
        });
    }
}
