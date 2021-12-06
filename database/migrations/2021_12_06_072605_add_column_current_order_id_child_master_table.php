<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCurrentOrderIdChildMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('child_master', function (Blueprint $table) {
       
            $table->integer('current_order_id')->nullable()->after('is_sponsored');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('child_master', function (Blueprint $table) {
            $table->dropColumn('current_order_id');

        });
    }
}
