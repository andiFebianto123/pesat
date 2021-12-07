<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatusUserIdHistoryStatusPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('history_status_payment', function (Blueprint $table) {
            //
            $table->enum('status', ['1', '2', '3'])->comment('1=menunggu pembayaran, 2=sudah dibayar, 3=batal')->after('detail_history');
            $table->string('status_midtrans')->after('status');
            $table->bigInteger('user_id')->nullable()->unsigned()->after('status_midtrans');

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
        Schema::table('history_status_payment', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('status_midtrans');
            $table->dropColumn('user_id');

        });
    }
}
