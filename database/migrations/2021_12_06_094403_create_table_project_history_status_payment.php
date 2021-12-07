<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProjectHistoryStatusPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_history_status_payment', function (Blueprint $table) {
            $table->increments('history_id');
            $table->longText('detail_history');
            $table->enum('status', ['1', '2', '3'])->comment('1=menunggu pembayaran, 2=sudah dibayar, 3=batal');
            $table->string('status_midtrans');
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_history_status_payment');
    }
}
