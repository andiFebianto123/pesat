<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDelivStatusDlpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('dlp', function (Blueprint $table) {
            //
            $table->enum('deliv_status', ['1', '2', '3'])->comment('1=belum dikirim, 2=dudah dikirim, 3=gagal dikirim')->default(1)->after('file_dlp');

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
        Schema::table('dlp', function (Blueprint $table) {
            $table->dropColumn('deliv_status');

        });
    }
}
