<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPriceAndIsSponsoredChildMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('child_master', function (Blueprint $table) {
            //
            $table->decimal('price',10,2)->after('fc');
            $table->boolean('is_sponsored')->default(0)->after('status_dlp');

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
        Schema::table('child_master', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('is_sponsored');
        });
    }
}
