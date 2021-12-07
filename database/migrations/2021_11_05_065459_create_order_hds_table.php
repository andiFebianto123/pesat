<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_hd', function (Blueprint $table) {
            $table->increments('order_id');
            $table->integer('parent_order_id')->nullable();
            $table->string('order_no',16);
            $table->integer('sponsor_id')->unsigned();
            $table->decimal('total_price',10,2);
            $table->enum('payment_status', ['1', '2', '3', '4'])->comment('1=menunggu pembayaran, 2=sudah dibayar, 3=kadaluarsa, 4=batal');
            $table->string('snap_token', 36)->nullable();
            $table->timestamps();
        });
        Schema::table('order_hd', function (Blueprint $table) {
            
            $table->foreign('sponsor_id')->references('sponsor_id')->on('sponsor_master')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {  
        Schema::table('order_hd', function (Blueprint $table) {
            
            $table->dropForeign(['sponsor_id']);
        });
        Schema::dropIfExists('order_hd');
    }
}
