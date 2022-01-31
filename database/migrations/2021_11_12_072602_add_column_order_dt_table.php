<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOrderDtTable extends Migration
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
            $table->date('start_order_date')->after('monthly_subscription');
            $table->date('end_order_date')->after('start_order_date');
            $table->boolean('has_child')->default(0)->after('end_order_date');
            $table->integer('has_remind')->default(0)->after('has_child');

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
            $table->dropColumn('start_order_date');
            $table->dropColumn('end_order_date');
            $table->dropColumn('has_child');
            $table->dropColumn('has_remind');
        });
    }
}
