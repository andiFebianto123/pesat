<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOrderProjectIdMidtransOrderProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_project', function (Blueprint $table) {
            //
            $table->string('order_project_id_midtrans')->nullable()->after('order_project_id');

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
        Schema::table('order_project', function (Blueprint $table) {
            $table->dropColumn('order_project_id_midtrans');

        });
    }
}
