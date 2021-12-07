<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_dt', function (Blueprint $table) {
            $table->increments('order_dt_id');
            $table->integer('order_id')->unsigned();
            $table->integer('child_id')->unsigned();
            $table->decimal('price',10,2);
            $table->integer('monthly_subscription');
            $table->timestamps();
        });
        Schema::table('order_dt', function (Blueprint $table) {
            
            $table->foreign('order_id')->references('order_id')->on('order_hd')->onDelete('restrict')->onUpdate('cascade');
        });
        Schema::table('order_dt', function (Blueprint $table) {
            
            $table->foreign('child_id')->references('child_id')->on('child_master')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_dt', function (Blueprint $table) {
            
            $table->dropForeign(['order_id']);
        });
        Schema::table('order_dt', function (Blueprint $table) {
            
            $table->dropForeign(['child_id']);
        });
        
        Schema::dropIfExists('order_dt');
    }
}
