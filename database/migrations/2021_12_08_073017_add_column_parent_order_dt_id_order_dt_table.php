<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnParentOrderDtIdOrderDtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_dt', function (Blueprint $table) {
            //
            $table->integer('parent_order_dt_id')->nullable()->after('order_dt_id');

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
        Schema::table('order_dt', function (Blueprint $table) {
            $table->dropColumn('parent_order_dt_id');

        });
    }
}
