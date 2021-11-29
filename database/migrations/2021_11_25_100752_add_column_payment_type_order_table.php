<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPaymentTypeOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_hd', function (Blueprint $table) {
            //
            $table->string('payment_type')->nullable()->after('payment_status');

        });
        Schema::table('order_project', function (Blueprint $table) {
            //
            $table->string('payment_type')->nullable()->after('payment_status');

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
        Schema::table('order_hd', function (Blueprint $table) {
            $table->dropColumn('payment_type');

        });
        Schema::table('order_project', function (Blueprint $table) {
            $table->dropColumn('payment_type');

        });
    }
}
