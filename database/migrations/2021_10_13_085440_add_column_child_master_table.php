<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnChildMasterTable extends Migration
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
            $table->boolean('status')->default(1)->after('file_profile');
        });
        Schema::table('child_master', function (Blueprint $table) {
            //
            $table->boolean('status_dlp')->default(0)->after('status');
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
        // Schema::table('child_master', function (Blueprint $table) {
        //     $table->dropColumn('status');
        // });
        Schema::table('child_master', function (Blueprint $table) {
            $table->dropColumn('status_dlp');
        });
    }
}
